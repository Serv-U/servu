<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MediaManager
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Model_Mysql4_Productfiles_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mediamanager/productfiles');    
    }

}

?>