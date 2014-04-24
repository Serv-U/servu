<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SD_Acm_Model_Unsubscribe extends Mage_Core_Model_Abstract {
    
    public function _construct(){
        parent::_construct();
        $this->_init('sd_acm/unsubscribe');
    }
    
    public function loadCustomerId($id, $store)
    {
        $this->_getResource()->loadCustomerId($this, $id, $store);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;

    }
}
?>
