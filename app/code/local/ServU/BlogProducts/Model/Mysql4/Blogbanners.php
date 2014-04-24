<?php

/**
 * @desc Description of BlogBanners
 * @author andrewprendergast
 */
class ServU_BlogProducts_Model_Mysql4_Blogbanners extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {
        $this->_init('blogproducts/blogbanners', 'id');
    }
}
?>