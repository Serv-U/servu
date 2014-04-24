<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layered Navigation block for search
 *
 */
class SD_AdvancedAttributes_Block_Catalogsearch_Layer extends Mage_CatalogSearch_Block_Layer
{
    protected function _construct() {
        parent::_construct();
        if (Mage::getStoreConfig('advancedattributes/settings/price_slider')) {
            $this->_priceFilterBlockName        = 'advancedattributes/catalog_layer_filter_price_range';
            $this->_priceBlockNameInLayout 		= 'layer_filter_price_range';
        }

        $this->_attributeFilterBlockName    = 'advancedattributes/catalog_layer_filter_attribute';
    }
    
    public function getFilters()
    {

        // Get currently active filters
        $activeArray = array();
        $_activeFilters = Mage::getSingleton('Mage_Catalog_Block_Layer_State')->getActiveFilters();

        $_category = Mage::registry('current_category');
        
        $hasCategoryFilter = false;
        $categoryFilterId = 0;
        $currentCategory = 0;
       
        foreach ($_activeFilters as $_active) {
            $activeArray[] = $_active->getValue();
            $filters = get_class_methods($_active->getFilter());
            
            if(method_exists($_active->getFilter(), 'getCategory') && !$hasCategoryFilter) {
                $hasCategoryFilter = true;
                $categoryFilterId = $_active->getFilter()->getCategory()->getId();;
            }
            
        }

        $filters = array();
        if ($categoryFilter = $this->_getCategoryFilter()) {
            $filters[] = $categoryFilter;
        }

        // Get all filterable attributes
        $filterableAttributes = $this->_getFilterableAttributes();
        //Get the current category which is based on first if there is a cat filter
        //and if not, then use category
        if ($categoryFilterId != 0) {
            $currentCategory = $categoryFilterId;
        } else if ($_category) {
            $currentCategory = $_category->getId();
        }

        // Only allow any dependent filters in if the dependency
        // has already been selected.
        foreach ($filterableAttributes as $attribute) {
            $model = Mage::getSingleton('advancedattributes/advancedattributes')->loadFromAttributeId($attribute->getId());
            $depends = array();
            $exemptCategories = array();
            if($model->getDependantOptions() != '') {
                $depends = explode(',',$model->getDependantOptions());
            }
            
            if($model->getExemptCategories() != '') {
                $exemptCategories = explode(',',$model->getExemptCategories());
            }
            
            if(count($exemptCategories ) > 0 && $hasCategoryFilter == false)  {
                if(! (in_array($currentCategory, $exemptCategories))) {
                    if (count($depends) > 0) {
                        if (array_intersect($depends, $activeArray)) {
                            $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter');
                        }  
                    } else {
                        $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter');
                    }
                }
            } else {
                if (count($depends) > 0) {
                    if (array_intersect($depends, $activeArray)) {
                        $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter');
                    }  
                } else {
                    $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter');
                }
            }
            
        }
        return $filters;
    }

}
