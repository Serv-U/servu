<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ServU_Emailtracking_Model_Emailtracking extends Mage_Core_Model_Abstract{
    
    public function _construct(){
        parent::_construct();
        $this->_init('servu_emailtracking/emailtracking');
    }

}
?>
