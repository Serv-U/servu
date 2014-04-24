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
class SD_Acm_Adminhtml_HistoryController extends Mage_Adminhtml_Controller_Action {
    
    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/acm/history');
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('newsletter/history');

        return $this;
    }

    public function indexAction() {  
        $this->displayTitle('History');

        $this->_initAction()
                ->renderLayout();
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
