<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SD_ReviewReminder_Model_Emails extends Mage_Core_Model_Abstract {
    
    public function _construct(){
        parent::_construct();
        $this->_init('reviewreminder/emails');
    }
    
    public function loadByOrderId($id)
    {
        $this->_getResource()->loadByOrderId($this, $id);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
    
    public function loadByOrderIdEmailNum($id,$emailNumber)
    {
        $this->_getResource()->loadByQuoteIdEmailNum($this, $id, $emailNumber);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;

    }
}
?>
