<?php
class SD_AdvancedAttributes_Block_Layer_Filter_Decimal extends SD_AdvancedAttributes_Block_Layer_MultiFilter_Attribute {

	public function __construct() {
		parent::__construct();
                $this->_filterModelName = 'advancedattributes/catalog_layer_filter_decimal';
	}

	public function addFacetCondition() {
		$this->_filter->addFacetCondition();
		return $this;
	}
}
?>