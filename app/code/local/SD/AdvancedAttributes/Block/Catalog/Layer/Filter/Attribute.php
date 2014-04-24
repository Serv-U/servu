<?php
class SD_AdvancedAttributes_Block_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    protected $_advancedFilter;
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('advancedattributes/catalog/layer/filter.phtml');
        $this->_filterModelName = 'advancedattributes/catalog_layer_filter_attribute';
        //$this->_filterModelName = 'catalog/layer_filter_attribute';
    }
    
    protected function _prepareFilter() {
        $this->_advancedFilter = Mage::getModel('advancedattributes/advancedattributes')->loadFromAttributeId($this->getAttributeModel()->getId());
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
    
    /**
    * Instantiate and prepare filter model.
    *
    * @return SD_AdvancedAttributes_Block_Layer_MultiFilter_Abstract
    */
    protected function _initFilter() {

        if (!$this->_filterModelName) {
                Mage::throwException(Mage::helper('catalog')->__('Filter model name must be declared.'));
        }

        $this->_filter = Mage::getModel($this->_filterModelName)
                ->setLayer($this->getLayer());

        $this->_prepareFilter();

        return $this;
    }

    /**
     * @return SD_AdvancedAttributes_Model_Layer_MultiFilter_Attribute
     */
    public function getFilter() {
        return $this->_filter;
    }

    /**
     * Check if some item of the current filter was applied to the collection.
     */
    public function hasFiltersInState() {
        $filterItems = $this->getLayer()->getState()->getFilters();
        /** @var $filterItem Mage_Catalog_Model_Layer_Filter_Item */
        foreach ($filterItems as $filterItem) {
                if ($filterItem->getFilter() == $this->getFilter()) {
                        return true;
                }
        }

        return false;
    }

    /**
     * Check if this filter can be shown.
     */
    public function canBeShown() {
        return $this->getItemsCount() > 0;
    }

    public function addFacetCondition() {
        $this->_filter->addFacetCondition();
        return $this;
    }
    
    public function getDisplayType() {
        return $this->_advancedFilter->getDisplayType();
    }
    
    public function getTooltip() {
        return $this->_advancedFilter->getToolTip();
    }
    
    public function getIsCollapsed() {
        return $this->_advancedFilter->getIsCollapsed();
    }
    
    public function getUnfoldedOptions() {
        return $this->_advancedFilter->getUnfoldedOptions();
    }
    
    public function getGroupName() {
        return $this->_filter->getGroupName();
    }
    
    public function getRequestVar() {
        return $this->_filter->getRequestVar();
    }

    /*public function getImage() {
        return $this->_filter->getImage();
    }*/
}