<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SD_Acm_Model_Emails extends Mage_Core_Model_Abstract {
    
    public function _construct(){
        parent::_construct();
        $this->_init('sd_acm/emails');
    }
    
    public function loadByQuoteId($id)
    {
        $this->_getResource()->loadByQuoteId($this, $id);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;

    }
    
    public function loadByQuoteIdEmailNum($id,$emailNumber)
    {
        $this->_getResource()->loadByQuoteIdEmailNum($this, $id, $emailNumber);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;

    }
}
?>
