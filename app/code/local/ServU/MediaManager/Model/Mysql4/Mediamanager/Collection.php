<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mediamanager
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Model_Mysql4_Mediamanager_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mediamanager/mediamanager');    
    }
    
    protected function _initSelect()
    {
    	parent::_initSelect();
        return $this;
    }
    
}

?>
