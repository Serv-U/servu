<?php
class SD_AdvancedAttributes_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View
{
    protected function _construct() {
        parent::_construct();

        if (Mage::getStoreConfig('advancedattributes/settings/price_slider')) {
            $this->_priceFilterBlockName        = 'advancedattributes/catalog_layer_filter_price_range';
            $this->_priceBlockNameInLayout 		= 'layer_filter_price_range';
        }

        $this->_attributeFilterBlockName    = 'advancedattributes/catalog_layer_filter_attribute';
        $this->_decimalFilterBlockName = 'advancedattributes/catalog_layer_filter_decimal';
    }
    
    /**
     * Get all layer filters
     *
     * @return array
     */
    public function getFilters()
    {
        // Get currently active filters
        $activeArray = array();
        $_activeFilters = Mage::getSingleton('Mage_Catalog_Block_Layer_State')->getActiveFilters();
       
        $_category = Mage::registry('current_category');
        $currentCategory = 0;
       
        foreach ($_activeFilters as $_active) {
            $activeArray[] = $_active->getValue();
        }

        $filters = array();
        if ($categoryFilter = $this->_getCategoryFilter()) {
            $filters[] = $categoryFilter;
        }

        // Get all filterable attributes
        $filterableAttributes = $this->_getFilterableAttributes();
        //Get the current category which is based on first if there is a cat filter
        //and if not, then use category
        if (Mage::app()->getRequest()->getParam('cat') ) {
            $currentCategory = Mage::app()->getRequest()->getParam('cat');
        } else if ($_category) {
            $currentCategory = $_category->getId();
        }
        // Only allow any dependent filters in if the dependency
        // has already been selected.
        foreach ($filterableAttributes as $attribute) {
            $model = Mage::getSingleton('advancedattributes/advancedattributes')->loadFromAttributeId($attribute->getId());
            $dependAttributes = array();
            $exemptCategories = array();
            if($model->getDependantOptions() != '') {
                $dependAttributes  = explode(',',$model->getDependantOptions());
            }
            
            if($model->getExemptCategories() != '') {
                $exemptCategories = explode(',',$model->getExemptCategories());
            }
            
            if(count($exemptCategories ) > 0 /*&& $currentCategory != 0*/)  {
                if(! (in_array($currentCategory, $exemptCategories))) {
                    if (count($dependAttributes ) > 0) {
                        if (array_intersect($dependAttributes , $activeArray)) {
                            $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter');
                        }  
                    } else {
                        $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter');
                    }
                }
            } else {
                if (count($dependAttributes ) > 0) {
                    if (array_intersect($dependAttributes , $activeArray)) {
                        $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter');
                    }  
                } else {
                    $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter');
                }
            }
            
        }
        return $filters;
    }
    
    /**
    * Register our layered navigation model as current layered navigation.
    *
    */
    public function getLayer() {
            /** @var $layer SD_AdvancedAttributes_Model_Catalog_Layer */
            $layer = Mage::registry('current_layer');
            
            if (!($layer instanceof SD_AdvancedAttributes_Model_Catalog_Layer)) {
                $layer = null;
                Mage::unregister('current_layer');
            }

            if ($layer == null) {
                Mage::register('current_layer', Mage::getSingleton('advancedattributes/catalog_layer'));
                $layer = Mage::registry('current_layer');
                if (!method_exists('Mage_Catalog_Block_Product_List', 'getLayer')) {
                    Mage::unregister('_singleton/catalog/layer');
                    Mage::register('_singleton/catalog/layer', $layer);
                } else {
                    Mage::getSingleton('catalog/layer')->setState($layer->getState());
                }
            }
            
            if ($layer instanceof SD_AdvancedAttributes_Model_Catalog_Layer) {
                return $layer;
            } else {
                throw new Exception('Invalid layer class');
            }
    }
    
    /**
    * Handles sequence of the layered navigation initialization, filters impact to the collection
    * and calculation of filter items to show.
    */
    protected function _prepareLayout() {
            Mage::app()->setUseSessionInUrl(false);

            $stateBlock = $this->getLayout()->createBlock($this->_stateBlockName)
                    ->setLayer($this->getLayer());

            /** @var $categoryBlock SD_AdvancedAttributes_Block_Catalog_Layer_MultiFilter_Category */
            $categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
                    ->setLayer($this->getLayer())
                    ->init();

            $this->setChild('layer_state', $stateBlock);
            $this->setChild('category_filter', $categoryBlock);

            $blocks = array();
            if ($categoryBlock->getFilter()) {
                    $blocks[] = $categoryBlock;
            }

            $this->getLayer()->getProductCollection()->cloneSelect();
            $hasItems = count($this->getLayer()->getProductCollection()->getAllIds());

            $filterableAttributes = $this->_getFilterableAttributes();
            foreach ($filterableAttributes as $attribute) {
                    if ($attribute->getAttributeCode() == 'price') {
                            $block = $this->getLayout()->createBlock($this->_priceFilterBlockName, $this->_priceBlockNameInLayout);
                    } elseif ($attribute->getBackendType() == 'decimal') {
                            $block = $this->getLayout()->createBlock($this->_decimalFilterBlockName);
                    } else {
                            $block = $this->getLayout()->createBlock($this->_attributeFilterBlockName);
                    }

                    $block->setLayer($this->getLayer())
                                    ->setAttributeModel($attribute)
                                    ->init();

                    $this->setChild($attribute->getAttributeCode() . '_filter', $block);

                    if ($block->getFilter()) {
                            $blocks[] = $block;
                    }
            }
            $applyAfter = array();
            foreach ($blocks as $block) {
                    $filter = $block->getFilter();

                    /*if (!$this->isFilterCleared($filter)) {
                            if ($filter->getRequestVar() == 'price') {
                                    $applyAfter[] = $filter;
                                    continue;
                            }*/
                            $filter->apply($this->getRequest(),$filter);
                    /*}*/
            }

            foreach ($applyAfter as $filter) {
                    $filter->apply($this->getRequest(),$filter);
            }

            if ($hasItems && !count($this->getLayer()->getProductCollection()->getAllIds())) {
                    $this->getLayer()->getProductCollection()->useClonedSelect();
                    //$this->getDataHelper()->setNotUseFilter(true);
            }

            foreach ($blocks as $block) {
                    $block->getFilter()->getItems();
            }

            foreach ($blocks as $block) {
                    $block->getFilter()->updateStateItemsStatus();
            }

            $this->getLayer()->apply();
    }
    
    protected $_stateBlockName = 'catalog/layer_state';
    protected $_categoryBlockName = '';
    protected $_priceFilterBlockName = '';
    protected $_attributeFilterBlockName = '';

    protected $_priceBlockNameInLayout = '';
}