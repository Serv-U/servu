<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Collection
 *
 * @author dustinmiller
 */
class SD_Dropship_Model_Mysql4_Supplier_Collection 
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_previewFlag;

    protected function _construct()
    {
        $this->_init('sd_dropship/supplier');
        $this->_setIdFieldName('false_id');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('identifier', 'name');
    }

    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    protected function _initSelect()
    {
    	parent::_initSelect();

        $this->getSelect()
        	->joinRight(array('aov' => $this->getTable('eav/attribute_option_value')), 'aov.option_id = main_table.attribute_option_id AND aov.store_id = main_table.attribute_value_store_id', array('value_id', 'value', 'store_id', 'option_id'))
            ->join( array('ao'  => $this->getTable('eav/attribute_option')), 'ao.option_id = aov.option_id', array())
            ->join( array('a'   => $this->getTable('eav/attribute')), 'a.attribute_id = ao.attribute_id', array(
                'attribute_code', 'frontend_label','concat(a.attribute_code, ao.option_id, \'store\', aov.store_id) as false_id'))
            ->where('a.attribute_code IN (?)', 'supplier')
            ->where('SUBSTRING(aov.value,1,1) != \'-\'')
            ->order('value');

        return $this;
    }

    /**
     * Format URL key from name or defined key
     *
     * @param string $str
     * @return string
     */
    public static function formatUrlKey($str)
    {
        $str = Mage::helper('core')->removeAccents($str);
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }

    protected function _afterLoad()
    {
    	parent::_afterLoad();

    	foreach ($this->_items as $object) {
            if(!$object->getData('identifier'))         $object->setData('identifier', $this->formatUrlKey($object->getValue()));
        }

        return $this;
    }

    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return SD_Manager_Model_Mysql4_Manufacturer_Collection
     */
    public function addStoreFilter($store, $allStores = false)
    {  
        
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        if ($allStores && ($store > 0)) {
	        $this->getSelect()
		         ->where('(? in (aov.store_id, attribute_value_store_id)) OR (0 in (aov.store_id, attribute_value_store_id))', $store);
        } else {
	        $this->getSelect()
		         ->where('? in (aov.store_id, attribute_value_store_id)', $store);
        }
        //die((string) $this->getSelect());
        return $this;
    }

    /**
     * Add Filter by attribute_code
     *
     * @param int|Mage_Core_Model_Store $store
     * @return SD_Manager_Model_Mysql4_Manufacturer_Collection
     */
    public function addAttributeCodeFilter($attributeCode)
    {
        $this->getSelect()
        	 ->where('a.attribute_code = ?', $attributeCode);
        return $this;
    }

    /**
     * Add Filter for enabled
     *
     * @param int|Mage_Core_Model_Store $store
     * @return SD_Manager_Model_Mysql4_Manufacturer_Collection
     */
    public function addEnabledFilter()
    {
    	//the disabled flag must be set explicit
    	//if there is no record in values table, it means it the value page is not created yet, so the default is 1
        $this->getSelect()->where('coalesce(main_table.is_enabled,1) > 0');
        return $this;
    }
    
    public function addAttributeToSort($attribute, $dir='asc')
    {
        if (!is_string($attribute)) {
            return $this;
        }
        
        $this->setOrder($attribute, $dir);
        return $this;
    } 

    /**
     * Randomize the output
     *
     * @param int|Mage_Core_Model_Store $store
     * @return SD_Manager_Model_Mysql4_Manufacturer_Collection
     */
    public function randomize()
    {
        $this->getSelect()
    		->reset( Zend_Db_Select::ORDER )
        	->order('rand()');
        return $this;
    }

    /**
     * Remove pagination, get all items
     *
     * @param int|Mage_Core_Model_Store $store
     * @return SD_Manager_Model_Mysql4_Manufacturer_Collection
     */
    public function retrieveAll()
    {
        $this->setPageSize(false);

        //echo $this->getSelect();
        return $this;
    }

}

?>
