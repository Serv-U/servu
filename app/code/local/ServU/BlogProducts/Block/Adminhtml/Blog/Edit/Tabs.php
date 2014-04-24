<?php
/**
 * Description of Tabs
 * @author andrewprendergast
 */
class ServU_BlogProducts_Block_Adminhtml_Blog_Edit_Tabs extends AW_Blog_Block_Manage_Blog_Edit_Tabs {
 
    /**
     * @desc Completely overriding _beforeToHtml() to get order of tabs correct
     * @return object 
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('blog')->__('Post Information'),
            'title' => Mage::helper('blog')->__('Post Information'),
            'content' => $this->getLayout()->createBlock('blog/manage_blog_edit_tab_form')->toHtml(),
        ));

        $this->addTab('options_section', array(
            'label' => Mage::helper('blog')->__('Advanced Options'),
            'title' => Mage::helper('blog')->__('Advanced Options'),
            'content' => $this->getLayout()->createBlock('blog/manage_blog_edit_tab_options')->toHtml(),
        ));
        
        $this->addTab('related_products', array(
            'label'     => Mage::helper('blog')->__('Related Products'),
            'title'     => Mage::helper('blog')->__('Related Products'),
            'url'       => $this->getUrl('adminhtml/blogproducts/products', array('_current' => true)),
            'class'     => 'ajax',
        ));
        
        $this->addTab('banner_image', array(
            'label'     => Mage::helper('blog')->__('Banner HTML/Image'),
            'title'     => Mage::helper('blog')->__('Banner HTML/Image'),
            'content'   => $this->getLayout()->createBlock('blogproducts/adminhtml_blog_edit_tab_banner')->toHtml(),
        ));        
        
        return parent::_beforeToHtml();
    }
}
?>