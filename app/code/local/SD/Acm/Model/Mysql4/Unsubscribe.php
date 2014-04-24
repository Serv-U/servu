<?php
class SD_Acm_Model_Mysql4_Unsubscribe extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {  
        $this->_init('sd_acm/unsubscribe', 'id');
    }
    
    public function loadCustomerId(Mage_Core_Model_Abstract $object, $value, $store)
    {
     $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
			$select = $read->select()
				->from(array('main_table' => $this->getMainTable()))
                                ->where('main_table.customer_id = ?', $value)
                                ->where('main_table.store_id = ?', $store);

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }
        $this->_afterLoad($object);

        return $this;   
    }

    
}
?>
