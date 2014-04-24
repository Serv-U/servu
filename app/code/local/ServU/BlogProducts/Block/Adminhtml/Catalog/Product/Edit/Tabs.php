<?php

class ServU_BlogProducts_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Abstract
implements Mage_Adminhtml_Block_Widget_Tab_Interface {
    	
    public function canShowTab() {
        //Do not show tab on first page when creating a new product
        if (Mage::app()->getRequest()->getActionName() == 'new' && !$this->getRequest()->getParam('set', null)) {
            return false;
        }
        return true;
    }
    
    public function isHidden() {
    	return false;
    }
    
    public function getTabClass() {
        return 'ajax';
    }

    public function getSkipGenerateContent() {
        return true;
    }

    public function getCurrentUrl($params = array()) {
        return $this->getUrl('adminhtml/catalog_product/productblogsgrid', array('_current' => true));
    }

    public function getTabUrl() {
        return $this->getUrl('adminhtml/catalog_product/productblogs', array('_current' => true));
    }
	
    public function getTabLabel() {
        return 'Related Blog Posts';
    }
        
    public function getTabTitle() {
    	return $this->getTabLabel();
    }
}
