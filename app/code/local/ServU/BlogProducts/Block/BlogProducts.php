<?php

class ServU_BlogProducts_Block_BlogProducts extends Mage_Catalog_Block_Product {

    public function __construct() {
        parent::__construct();
        
        $post_id = Mage::getSingleton('blog/post')->getPostId();
        $collection = Mage::getModel('blogproducts/blogproducts')->getBlogCollection($post_id);
        $this->setItems($collection);
    }
}