<?php

class ServU_BlogProducts_Block_BlogBanners extends Mage_Core_Block_Template {

    public function __construct() {
//        parent::__construct();
        
        //Get Banner HTML
        $post_id = Mage::getSingleton('blog/post')->getPostId();
        $banner = Mage::getModel('blogproducts/blogbanners')->getBanner($post_id);
        $this->setBanner($banner);
    }
}