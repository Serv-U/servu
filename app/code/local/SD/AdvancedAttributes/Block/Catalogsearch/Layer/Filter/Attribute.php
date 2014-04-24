<?php
class SD_AdvancedAttributes_Block_Catalogsearch_Layer_Filter_Attribute extends Mage_CatalogSearch_Block_Layer_Filter_Attribute
{
    protected $_advancedFilter;
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('advancedattributes/catalog/layer/filter.phtml');
        $this->_filterModelName = 'catalogsearch/layer_filter_attribute';
    }
    
    protected function _prepareFilter() {
        $this->_advancedFilter = Mage::getModel('advancedattributes/advancedattributes')->loadFromAttributeId($this->getAttributeModel()->getId());
        $this->_filter->setAttributeModel($this->getAttributeModel());
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
    
    public function canBeShown() {
        return $this->getItemsCount() > 0;
    }
}
