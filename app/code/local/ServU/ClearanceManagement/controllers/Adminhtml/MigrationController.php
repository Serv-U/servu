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
class ServU_ClearanceManagement_Adminhtml_MigrationController extends Mage_Adminhtml_Controller_Action {
    
    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _initAction() {
        $this->_title($this->__('Migrate Products'))
                ->_title($this->__('Clearance Management'))
                ->_title($this->__('Catalog'));

        $this->loadLayout()
                ->_setActiveMenu('catalog/clearance');

        return $this;
    }
    
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/catalog/clearance/migration');
    }

    public function indexAction() {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('clearancemanagement/adminhtml_migration'))
            ->renderLayout();
    }
    
    /* Change the clearance attribute to 'Yes' */
    /* Change the clearance attribute to 'Yes' */
    public function massClearanceAction()
    {
        $productIds = $this->getRequest()->getParam('clearance');

        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
            $this->_redirect('*/*/index');
        }
        else {
            try {
                foreach ($productIds as $productId) {
                    $product = Mage::getSingleton('catalog/product')->load($productId);
                    $product->setClearanceItem(1);
                    if($product->getData('other_filterable_attributes') != '') {
                        $product->setOtherFilterableAttributes($product->getOtherFilterableAttributes(). ',5171');
                    } else {
                        $product->setOtherFilterableAttributes('5171');
                    }
                    $product->getResource()->saveAttribute($product, 'other_filterable_attributes');
                    $product->getResource()->saveAttribute($product, 'clearance_item');
                    $product->setOtherFilterableAttributes('');
                    //$product->getResource()->save($product);
                }
                $this->_getSession()->addSuccess($this->__('The products have been moved.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            $this->_redirect('*/migration/index');
        }
    }
}

?>

