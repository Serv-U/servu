<?php

/**
 * @desc Description of ProductBlogs
 * @author andrewprendergast
 */
class ServU_BlogProducts_Model_Mysql4_Productblogs extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {
        $this->_init('blogproducts/productblogs', 'id');
    }
}
?>