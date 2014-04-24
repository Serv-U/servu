<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of advancedattributes
 *
 * @author dustinmiller
 */
class SD_AdvancedAttributes_Model_Mysql4_Product_Collection extends Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection {
    
    protected $clonedSelect = null;

    /**
     * Add ability to change limitations from the outside of the collection.
     *
     * @param $filters
     */
    public function setLimitationFilters($filters) {
        $this->_productLimitationFilters = $filters;
    }

    /**
     * Do not unset category filter, instead restore it from the 'previous_category_id' key
     * otherwise it cause error with on of the enterprise modules.
     *
     * @return Varien_Db_Select
     */
    public function getProductCountSelect() {
        $currentSelect = clone $this->_select;
        $currentProductLimitations = $this->_productLimitationFilters;

        if (isset($this->_productLimitationFilters['previous_category_id'])) {
                $this->_productLimitationFilters['category_id'] = $this->_productLimitationFilters['previous_category_id'];
        }
        $this->_applyProductLimitations();
        $select = parent::getProductCountSelect();

        $this->_select = $currentSelect;
        
        $this->_productLimitationFilters = $currentProductLimitations;

        return $select;
    }

    /**
     * Filter collection by multiple category ids.
     *
     * @param array $categories
     * @return SD_AdvancedAttributes_Model_Mysql4_Product_Collection
     */
    public function addCategoriesFilter(array $categories) {
        if (isset($this->_productLimitationFilters['category_id'])) {
                $this->_productLimitationFilters['previous_category_id'] = $this->_productLimitationFilters['category_id'];
        }
        $this->_productLimitationFilters['category_id'] = $categories;
        unset($this->_productLimitationFilters['category_is_anchor']);
        $this->getSelect()->distinct(true);
        $this->_applyProductLimitations();

        return $this;
    }

    protected function _applyProductLimitations() {
        $this->_prepareProductLimitationFilters();
        $this->_productLimitationJoinWebsite();
        $this->_productLimitationJoinPrice();
        $filters = $this->_productLimitationFilters;

        if (!isset($filters['category_id']) && !isset($filters['visibility'])) {
                return $this;
        }

        $conditions = array(
                'cat_index.product_id=e.entity_id',
                $this->getConnection()->quoteInto('cat_index.store_id=?', $filters['store_id'])
        );
        if (isset($filters['visibility']) && !isset($filters['store_table'])) {
                $conditions[] = $this->getConnection()
                        ->quoteInto('cat_index.visibility IN(?)', $filters['visibility']);
        }

        $categoryIds = is_array($filters['category_id']) ? $filters['category_id'] : array($filters['category_id']);

        $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.category_id IN(?)', $categoryIds);

        if (isset($filters['category_is_anchor'])) {
                $conditions[] = $this->getConnection()
                        ->quoteInto('cat_index.is_parent=?', $filters['category_is_anchor']);
        }

        $joinCond = join(' AND ', $conditions);
        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_index'])) {
                $fromPart['cat_index']['joinCondition'] = $joinCond;
                $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        else {
                $this->getSelect()->join(
                        array('cat_index' => $this->getTable('catalog/category_product_index')),
                        $joinCond,array()
                        //array('cat_index_position' => 'position')
                );
        }

        $this->_productLimitationJoinStore();

        Mage::dispatchEvent('catalog_product_collection_apply_limitations_after', array(
                'collection'    => $this
        ));

        //$trace=debug_backtrace();
        //$caller=array_shift($trace);
        //list(, $caller) = debug_backtrace(false);
        //Mage::log("Called by {$caller['function']}");

        //if (isset($caller['class']))
            //Mage::log(" in {$caller['class']}");
        
        return $this;
    }

    public function addAttributeToSort($attribute, $dir = 'asc') {
        if ($attribute == 'position') {
                if (isset($this->_joinFields[$attribute])) {
                        $this->getSelect()->order("{$attribute} {$dir}");
                        return $this;
                }
                $this->getSelect()->order("cat_index.position {$dir}");
                // optimize if using cat index
                $filters = $this->_productLimitationFilters;
                if (isset($filters['category_id']) || isset($filters['visibility'])) {
                        $this->getSelect()->order('cat_index.product_id ' . $dir);
                }
                else {
                        $this->getSelect()->order('e.entity_id ' . $dir);
                }

                return $this;
        }
        return parent::addAttributeToSort($attribute, $dir);
    }

    public function cloneSelect() {
        $this->clonedSelect = clone $this->_select;
        return $this;
    }

    public function useClonedSelect() {
        $this->_setIsLoaded(false);
        $this->_items = array();
        $this->_data = null;
        $this->_isFiltersRendered = false;
        $this->_select = $this->clonedSelect;
        $this->load();
        return $this;
    }
}

?>
