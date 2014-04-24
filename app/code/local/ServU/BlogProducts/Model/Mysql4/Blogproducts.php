<?php

/**
 * @desc Description of BlogProducts
 * @author andrewprendergast
 */
class ServU_BlogProducts_Model_Mysql4_Blogproducts extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {
        $this->_init('blogproducts/blogproducts', 'id');
    }
    
//    public function addGridPosition($collection,$manager_id){
//    }
}
?>