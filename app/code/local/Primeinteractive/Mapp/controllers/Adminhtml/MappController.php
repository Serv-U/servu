<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * DISCLAIMER
 *
 *
 * @category   Primeinteractive
 * @package    Primeinteractive_Mapp
 * @version    1.0
 * @copyright   Copyright (c) 2012 Prime Interactive, Inc.
 */

class Primeinteractive_Mapp_Adminhtml_MappController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('catalog/mapp')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('mapp/mapp')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                    $model->setData($data);
            }

            Mage::register('mapp_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('catalog/mapp');
//            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
//            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('mapp/adminhtml_mapp_edit'))
                    ->_addLeft($this->getLayout()->createBlock('mapp/adminhtml_mapp_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mapp')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

/*
    public function sendmappAction() {
        $params = $this->getRequest()->getParams();
        $this->sendTransactionalEmail($params);
    }
*/

    public function emailmappAction() {
        //Send Email
        $id = $this->getRequest()->getParam('id');
        Mage::getModel('mapp/mapp')->sendMappEmail($id);
        
        //Set Confirmation Message
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mapp')->__('A mapp email has been sent to this client.'));
        
        
        //Redirect back to edit page
        $this->getRequest()->setParam('id', $id);
        $this->_forward('edit');
    }
    
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $params = $this->getRequest()->getParams();
            
            $model = Mage::getModel('mapp/mapp');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));
            
            //Set Product price
            $product = Mage::getModel('catalog/product'); 
            $productId = $product->getIdBySku($params['sku']);
            if($productId) {
                $obj = Mage::getModel('catalog/product');
                $_product = $obj->load($productId);
                $MappPrice = $_product->getMapPrice();
            }            
            if ($model->getMapprice == NULL){
                $model->setMapprice($MappPrice);
            }

            try {
                //Set Create time if NULL
                $date = new DateTime();
                if ($model->getCreatedTime() == NULL) {
                    $model->setCreatedTime($date->format('Y-m-d H:i:s'));
                }
                //Set Update Time
                $model->setUpdateTime($date->format('Y-m-d H:i:s'));
                
                //Generate Random Coupon Code
                if($model->getCouponCode() == NULL){
                    $coupon_code = Mage::helper('mapp/data')->generateCouponCode();
                    $model->setCouponCode($coupon_code);
                }
                
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mapp')->__('Mapp request was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mapp')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('mapp/mapp');
                $model->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $mappIds = $this->getRequest()->getParam('mapp');
        if(!is_array($mappIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($mappIds as $mappId) {
                    $mapp = Mage::getModel('mapp/mapp')->load($mappId);
                    $mapp->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($mappIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

//    public function massStatusAction() {
//        $mappIds = $this->getRequest()->getParam('mapp');
//        if(!is_array($mappIds)) {
//            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
//        } else {
//            try {
//                foreach ($mappIds as $mappId) {
//                    $mapp = Mage::getSingleton('mapp/mapp')
//                        ->load($mappId)
//                        ->setStatus($this->getRequest()->getParam('status'))
//                        ->setIsMassupdate(true)
//                        ->save();
//                }
//                $this->_getSession()->addSuccess(
//                    $this->__('Total of %d record(s) were successfully updated', count($mappIds))
//                );
//            } catch (Exception $e) {
//                $this->_getSession()->addError($e->getMessage());
//            }
//        }
//        $this->_redirect('*/*/index');
//    }
}