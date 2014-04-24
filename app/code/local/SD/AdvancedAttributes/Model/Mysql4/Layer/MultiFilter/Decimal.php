<?php 
class SD_AdvancedAttributes_Model_Mysql4_Layer_MultiFilter_Decimal extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal {

	public function applyFiltersToCollection($filter, array $filtersToApply) {
		$collection = $filter->getLayer()->getProductCollection();
		$attribute  = $filter->getAttributeModel();
		$connection = $this->_getReadAdapter();
		$tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
		$conditions = array(
			"{$tableAlias}.entity_id = e.entity_id",
			$connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
			$connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId())
		);

		$collection->getSelect()->join(
			array($tableAlias => $this->getMainTable()),
			implode(' AND ', $conditions),
			array()
		);

		$select = $collection->getSelect();

		$i = 1;
		$from = null;
		$to = null;
		foreach ($filtersToApply as $filter) {
			$range = $filter['range'];
			$index = $filter['index'];
			if ($i == 1) {
				$i++;
				$select->where("{$tableAlias}.value >= ?", $range * ($index - 1));
			} else {
				$select->orWhere("{$tableAlias}.value >= ?", $range * ($index - 1));
			}
			$select->where("{$tableAlias}.value < ?", $range * $index);
		}


		return $this;
	}

	protected function _getSelect($filter) {
		$select = parent::_getSelect($filter);
		$wherePart = $select->getPart(Varien_Db_Select::WHERE);
		$newWherePart = array();

		$attribute  = $filter->getAttributeModel();
		$tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());

		$firstPart = true;
		foreach($wherePart as $where) {
			if (strpos($where, $tableAlias) || strpos($where, $tableAlias)) {
				continue;
			}

			if ($firstPart) {
				$where = preg_replace('/^AND/', '', trim($where));
				$firstPart = false;;
			}

			$newWherePart[] = $where;
		}

		$select->setPart(Varien_Db_Select::WHERE, $newWherePart);

		return $select;
	}
}
?>