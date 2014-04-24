<?php

class ServU_BlogProducts_Block_ProductBlogs extends Mage_Core_Block_Template {

    public function __construct() {
        parent::__construct();
        
        $product_id = Mage::registry('current_product')->getId();
        $collection = Mage::getModel('blogproducts/productblogs')->getBlogCollectionByProductId($product_id);
        $this->setBlogs($collection);
    }
}