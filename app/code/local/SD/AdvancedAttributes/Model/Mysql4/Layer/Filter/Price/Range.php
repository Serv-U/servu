<?php 

/**
 * Resource model for the price range filter.
 */
class SD_AdvancedAttributes_Model_Mysql4_Layer_Filter_Price_Range
			extends SD_AdvancedAttributes_Model_Mysql4_Layer_MultiFilter_Price {

	/**
	 * Returns minimal price of the collection products.
	 *
	 * @param $filter
	 * @return string
	 */
	public function getMinPrice($filter) {
            $select     = $this->_getSelect($filter);
            $connection = $this->_getReadAdapter();
            $response   = $this->_dispatchPreparePriceEvent($filter, $select);

            $table = $this->_getIndexTableAlias();

            $additional   = join('', $response->getAdditionalCalculations());
            $maxPriceExpr = new Zend_Db_Expr("MIN({$table}.min_price {$additional})");

            if (method_exists($this, '_replaceTableAlias')) {
                    $maxPriceExpr = $this->_replaceTableAlias($maxPriceExpr);
            }

            $select->columns(array($maxPriceExpr));

            return $connection->fetchOne($select) * $filter->getCurrencyRate();
	}

	/**
	 * Returns maximal price of the collection products.
	 *
	 * @param $filter
	 * @return string
	 */
	public function getMaxPrice($filter) {   
            
            $select     = $this->_getSelect($filter);
            
            $connection = $this->_getReadAdapter();
            $response   = $this->_dispatchPreparePriceEvent($filter, $select);
           
            $table = $this->_getIndexTableAlias();

            $additional   = join('', $response->getAdditionalCalculations());
            $maxPriceExpr = new Zend_Db_Expr("MAX({$table}.max_price {$additional})");

            if (method_exists($this, '_replaceTableAlias')) {
                    $maxPriceExpr = $this->_replaceTableAlias($maxPriceExpr);
            }

            $select->columns(array($maxPriceExpr));

            return $connection->fetchOne($select) * $filter->getCurrencyRate();
	}

	/**
	 * Add conditions to the select object of the collection.
	 *
	 * @param SD_AdvancedAttributes_Model_Catalog_Layer_Filter_Price_Range $filter
	 * @return SD_AdvancedAttributes_Model_Catalog_Layer_Filter_Price_Range
	 */
	public function applyFilterRangeToCollection(SD_AdvancedAttributes_Model_Catalog_Layer_Filter_Price_Range $filter) {

            $collection = $filter->getLayer()->getProductCollection();
            $collection->addPriceData($filter->getCustomerGroupId(), $filter->getWebsiteId());

            $select     = $collection->getSelect();
            $response   = $this->_dispatchPreparePriceEvent($filter, $select);

            $table      = $this->_getIndexTableAlias();
            $additional = join('', $response->getAdditionalCalculations());
            $rate       = $filter->getCurrencyRate();


            $priceExprMin  = new Zend_Db_Expr("(({$table}.min_price {$additional}) * {$rate})");
            $priceExprMax  = new Zend_Db_Expr("(({$table}.max_price {$additional}) * {$rate})");

            if ($filter->getAppliedPriceMin()) {
                    $select->where($priceExprMax.' >= ?', $filter->getAppliedPriceMin());
            }

            if ($filter->getAppliedPriceMax()) {
                    $select->where($priceExprMin. ' <= ?', $filter->getAppliedPriceMax());
            }

            return $this;
	}
}
?>