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
class SD_Acm_Adminhtml_IndividualController extends Mage_Adminhtml_Controller_Action {
    
    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/acm/individual');
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('newsletter/individual');

        return $this;
    }

    public function indexAction() {    
        $this->displayTitle('Individual Statistics');

        $this->_initAction()
                ->renderLayout();
    } 
    
    public function editAction() {
        if($id = $this->getRequest()->getParam('id')){
            $individual = Mage::getModel('sd_acm/acm')->load($id);

            //Check that individual exists
            if (!$individual->getData('id')){
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sd_acm')->__('Individual does not exist'));
                $this->_redirect('*/*/');
            }
            
            //Set data
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {    
                $individual->setData($data);
            }
            Mage::register('acm_data', $individual);

            //Handle Layout
            $this->loadLayout();
            $this->_setActiveMenu('newsletter/acm/individual');
            $this->displayTitle('Edit Individual Abandoned Cart Mailer');
            $this->_addContent($this->getLayout()->createBlock('acm/adminhtml_individual_edit'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sd_acm')->__('Individual ID is not set'));
            $this->_redirect('*/*/');
        }
    }
    
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            
            // try to save
            try {
                $model = Mage::getModel('sd_acm/acm')->load($id);
                $model->setData('comments', $data['comments']);
                $model->setData('yesnoContacted', $data['yesnoContacted']);
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Individual was successfully saved'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
               
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('adminhtml/individual/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
                // go to grid
                $this->_redirect('adminhtml/individual/index');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('adminhtml/individual/index');
                return;
            }
        }
    }
    
    public function massDeactivateAction() {  
        $ids = $this->getRequest()->getParam('acm');
        
        if(!is_array($ids)) {
               Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $acm = Mage::getModel('sd_acm/acm')->load($id);
                    $acm->setData('is_active', 0);
                    try {
                        $acm->save();
                    } catch (Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deactivated', count($ids)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function massActivateAction() {
        $ids = $this->getRequest()->getParam('acm');
        
        if(!is_array($ids)) {
               Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $acm = Mage::getModel('sd_acm/acm')->load($id);
                    $acm->setData('is_active', 1);
                    try {
                        $acm->save();
                    } catch (Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deactivated', count($ids)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
    
    protected function displayTitle($data = null, $root = 'Abandoned Cart Mailer') {
        if (!Mage::helper('sd_acm')->magentoVersion()) {
            if ($data) {
                if (!is_array($data)) {
                    $data = array($data);
                }
                foreach ($data as $title) {
                    $this->_title($this->__($title));
                }
                $this->_title($this->__($root));
            } else {
                $this->_title($this->__('Abandoned Cart Mailer'))->_title($root);
            }
        }
        return $this;
    }
}

?>
