<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BlogProducts
 *
 * @author andrewprendergast
 */
class ServU_BlogProducts_Model_Mysql4_Blogproducts_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('blogproducts/blogproducts');    
    }
    
    protected function _initSelect() {
    	parent::_initSelect();
        return $this;
    }
}
?>
