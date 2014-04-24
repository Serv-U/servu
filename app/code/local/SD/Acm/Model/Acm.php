<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SD_Acm_Model_Acm extends Mage_Core_Model_Abstract {
    
    public function _construct(){
        parent::_construct();
        $this->_init('sd_acm/acm');
    }
    
    public function loadByQuoteId($id)
    {
        $this->_getResource()->loadByQuoteId($this, $id);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;

    }

}
?>
