<?php
class SD_Acm_Model_Mysql4_Emails extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {  
        $this->_init('sd_acm/emails', 'id');
    }
    
    public function loadByQuoteId(Mage_Core_Model_Abstract $object, $value)
    {
     $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
			$select = $read->select()
				->from(array('main_table' => $this->getMainTable()))
                                ->where('main_table.quote_id = ?', $value);

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }
        $this->_afterLoad($object);

        return $this;   
    }
    
    public function loadByQuoteIdEmailNum(Mage_Core_Model_Abstract $object, $id, $emailNumber)
    {
     $read = $this->_getReadAdapter();
        if ($read && !is_null($id) && !is_null($emailNumber)) {
			$select = $read->select()
				->from(array('main_table' => $this->getMainTable()))
                                ->where('main_table.quote_id = ?', $id)
                                ->where('main_table.email_number = ?', $emailNumber);

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
