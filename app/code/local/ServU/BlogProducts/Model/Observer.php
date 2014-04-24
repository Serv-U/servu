<?php
 
class ServU_BlogProducts_Model_Observer {
    /**
     * Flag to stop observer executing more than once
     *
     * @var static bool
     */
    static protected $_singletonFlag = false;
 
    /**
     * This method will run when the blog post is saved from the Magento Admin
     * @param Varien_Event_Observer $observer
     */
    public function saveBlogProductRelationship(Varien_Event_Observer $observer) {
        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;
 
            try {
                //Get Blog Id from post data for existing blog posts
                $blog_id = Mage::app()->getRequest()->getParam('id');
                //Get Blog Id from database for new blog posts
                if(empty($blog_id)){
                    $lastRecord = Mage::getModel('blog/post')
                                    ->getCollection()
                                    ->setOrder('post_id')
                                    ->setPageSize(1);
                    foreach($lastRecord as $record){
                        $blog_id = $record->getPostId();
                    }
                }

                //Save Banner HTML
                if($banner = $this->_getRequest()->getPost('banner')){
                    Mage::getModel('blogproducts/blogbanners')->saveBanner($blog_id, $banner);
                } elseif ($this->_getRequest()->getPost('banner') == null){
                    //Remove Banner HTML if deleted (if statement above will not handle nulls)
                    Mage::getModel('blogproducts/blogbanners')->saveBanner($blog_id, '');
                }


                //Save Relationships
                if($blogproducts = $this->_getRequest()->getPost('links')){
                    //Save product relationships
                    foreach($blogproducts as $prod){
                        $prod_ids = Mage::helper('adminhtml/js')->decodeGridSerializedInput($prod);
                        Mage::getmodel('blogproducts/blogproducts')->setBlogProductRelationships($blog_id, $prod_ids);
                    }
                }
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }
    
    /**
     * Delete blogproduct relationships when blog post is deleted from blog edit page
     * @param Varien_Event_Observer $observer
     */
    public function deleteBlogProductRelationship(Varien_Event_Observer $observer) {
        if($blog_id = Mage::app()->getRequest()->getParam('id')){
            Mage::getmodel('blogproducts/blogproducts')->deleteRelationshipsByBlogId($blog_id);
        }
    }
    
    /**
     * Mass delete blogproduct relationships when blog posts are deleted from blog grid
     * @param Varien_Event_Observer $observer
     */
    public function massdeleteBlogProductRelationship(Varien_Event_Observer $observer) {
        $blog_ids = Mage::app()->getRequest()->getParam('blog');
        
        foreach($blog_ids as $blog_id){
            Mage::getmodel('blogproducts/blogproducts')->deleteRelationshipsByBlogId($blog_id);
        }
    }    
    
    /**
     * Shortcut to getRequest
     */
    protected function _getRequest() {
        return Mage::app()->getRequest();
    }
    
    /**
     * This method will run when products are created or edited in the Magento Admin
     * @param Varien_Event_Observer $observer
     */
    public function saveProductBlogData(Varien_Event_Observer $observer) {
        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;
 
            try {
                if($productblogs = $this->_getRequest()->getPost('links')){
                    //Get Product Id from post data for existing products
                    $product_id = Mage::app()->getRequest()->getParam('id');
                    
                    //Get Product Id from database for new products
                    if(empty($product_id)){
                        $lastRecord = Mage::getModel('catalog/product')
                                        ->getCollection()
                                        ->setOrder('entity_id')
                                        ->setPageSize(1)
                                        ->getFirstItem();
                        $product_id = $lastRecord->getData('entity_id');
                    }
                    
                    //Save product relationships
                    foreach($productblogs as $blog){
                        $blog_ids = Mage::helper('adminhtml/js')->decodeGridSerializedInput($blog);
                        Mage::getmodel('blogproducts/productblogs')->setProductBlogRelationships($product_id, $blog_ids);
                    }
                }
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }
    
    /**
     * To reduce spam, this method returns error if comment contains a value in a hidden field
     * @param Varien_Event_Observer $observer
     */
    public function processBlogCommentSubmission(Varien_Event_Observer $observer) {
        $spam = Mage::app()->getFrontController()->getRequest()->getParam('confirm_email', false);
        if(!empty($spam)){
            Mage::getSingleton('customer/session')->addError('Unable to process request. Please try again.');
            header('Location: ' . Mage::helper("core/url")->getCurrentUrl());
            exit;
        }
    }
}