<?php
class SD_ReviewReminder_Model_Mysql4_ReviewReminder extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {  
        $this->_init('reviewreminder/reviewReminder', 'id');
    }
    
    public function loadByOrderId(Mage_Core_Model_Abstract $object, $value)
    {
     $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
			$select = $read->select()
				->from(array('main_table' => $this->getMainTable()))
                                ->where('main_table.order_id = ?', $value);

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }
        $this->_afterLoad($object);

        return $this;   
    }
    
    public function loadByTrackingCode(Mage_Core_Model_Abstract $object, $value)
    {
     $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
			$select = $read->select()
				->from(array('main_table' => $this->getMainTable()))
                                ->where('main_table.tracking_url = ?', $value);

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
