<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Filters
 *
 * @author dustinmiller
 */
class ServU_ClearanceManagement_Adminhtml_DisableController extends Mage_Adminhtml_Controller_Action {
    
    protected function _initAction() {
        $this->_title($this->__('Disable Clearance Products'))
                ->_title($this->__('Clearance Management'))
                ->_title($this->__('Catalog'));

        $this->loadLayout()
                ->_setActiveMenu('catalog/clearance');

        return $this;
    }
    
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/catalog/clearance/disable');
    }
    
    /* Change the clearance attribute to 'Yes' */
    public function massDisableAction()
    {
        $productIds = $this->getRequest()->getParam('clearance');

        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
            $this->_redirect('*/*/index');
        }
        else {
            try {
                foreach($productIds as $productId) {
                    $product = Mage::getSingleton('catalog/product')->load($productId);
                    $product->setStatus(2); //1 = Enabled 2 = Disabled
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('The products have been disabled.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            $this->_redirect('*/disable/index');
        }
    }
    
    public function indexAction() {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('clearancemanagement/adminhtml_disable'))
            ->renderLayout();
    }

}

?>
