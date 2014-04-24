<?php 
/**
 * Block that shows price filter as a slider.
 *
 */
class SD_AdvancedAttributes_Block_Catalog_Layer_Filter_Price_Range
			extends SD_AdvancedAttributes_Block_Catalog_Layer_Filter_Attribute {

	public function __construct() {
		parent::__construct();
		$this->setTemplate('advancedattributes/catalog/layer/price/range.phtml');
		$this->_filterModelName = 'advancedattributes/catalog_layer_filter_price_range';
	}

	/**
	 * Returns maximal and minimal price of products in the current filtered collection.
	 *
	 * @return array
	 */
	public function getConfig() {

		$filter = $this->getFilter();

		$config = array(
			'min_price' => floor($filter->getMinPriceInt()),
			'max_price'	=> ceil($filter->getMaxPriceInt())
		);

		return $config;
	}

	/**
	 * Determine if the filter can be shown. Only if it has some items to show.
	 *
	 * @return bool
	 */
	public function canBeShown() {
		return $this->getFilter()->canBeShown() && parent::canBeShown();
	}
}
?>