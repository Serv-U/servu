<?php 

/**
 * Model represents price filter as list of the fixed price ranges with ability of multi-select them.
 */
class SD_AdvancedAttributes_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price {

	/**
	 * Apply filters from the request.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @param $filterBlock
	 * @return SD_AdvancedAttributes_Model_Catalog_Layer_Filter_Price
	 */
	public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
		if (method_exists($this, '_renderRangeLabel')) {
			return $this->applyMage17($request, $filterBlock);
		}

		$filters = $request->getParam($this->getRequestVar());
		if (!is_array($filters)) {
			return $this;
		}

		$filtersToApply = array();
		foreach ($filters as $filterStr) {
			$filter = explode(',', $filterStr);
			if (count($filter) != 2) {
				continue;
			}

			list($index, $range) = $filter;
			if ((int)$index && (int)$range) {
				$filtersToApply[] = array('index' => (int) $index, 'range' => (int) $range);
				$this->getLayer()->getState()->addFilter(
					$this->_createItem($this->_renderItemLabel($range, $index), $filterStr)
				);
			}
		}

		if (count($filtersToApply) > 0) {
			$this->_applyToCollection($filtersToApply);
		}

		return $this;
	}

	public function applyMage17(Zend_Controller_Request_Abstract $request, $filterBlock) {
		$this->getMaxPriceInt();
		$filters = $request->getParam($this->getRequestVar());
		if (!is_array($filters)) {
			return $this;
		}
		$filtersToApply = array();
		$items = $this->getItems();
		foreach ($filters as $filterStr) {
			$filter = explode('-', $filterStr);
			foreach ($items as $item) {
				if ($item['value'] == $filterStr) {
					$filtersToApply[] = array('from' => $filter['0'], 'to' => $filter['1']);
					$this->getLayer()->getState()->addFilter($this->_createItem(
						$this->_renderRangeLabel($filter['0'], $filter['1']),
						$filterStr
					));
					break;
				}
			}
		}

		if (count($filtersToApply) > 0) {
			$this->_applyToCollection($filtersToApply);
		}

		return $this;
	}

	/**
	 * Actual apply to the collection.
	 *
	 * @param $filtersToApply
	 * @param bool $dummy
	 */
	protected function _applyToCollection($filtersToApply, $dummy = false) {
		$this->_getResource()->applyFiltersToCollection($this, $filtersToApply);
	}

	/**
	 * Applied filter items should be marked to be shown as checked checkbox
	 * otherwise they will be passed to the browser as hidden inputs.
	 */
	public function updateStateItemsStatus() {
		$helper = Mage::helper('advancedattributes');
		$helper->initFilterItems($this->getLayer()->getState(), $this->_items);
	}

	/**
	 * @return SD_AdvancedAttributes_Model_Catalog_Layer_Filter_Price
	 */
	protected function _getResource() {
		if (is_null($this->_resource)) {
			$this->_resource = Mage::getResourceModel('advancedattributes/layer_multiFilter_price');
		}
		return $this->_resource;
	}

}
?>
