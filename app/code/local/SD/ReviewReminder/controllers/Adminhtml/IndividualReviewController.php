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
class SD_ReviewReminder_Adminhtml_IndividualReviewController extends Mage_Adminhtml_Controller_Action {
    
    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/review_ratings/reviewreminder/individual');
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('catalog');

        return $this;
    }

    public function indexAction() {   
        $this->displayTitle('Individual Statistics');

        $this->_initAction()
                ->renderLayout();
    } 
    
    protected function displayTitle($data = null, $root = 'Review Reminder Individual Stats') {
        if (!Mage::helper('reviewreminder')->magentoVersion()) {
            if ($data) {
                if (!is_array($data)) {
                    $data = array($data);
                }
                foreach ($data as $title) {
                    $this->_title($this->__($title));
                }
                $this->_title($this->__($root));
            } else {
                $this->_title($this->__('Review Reminder Individual Stats'))->_title($root);
            }
        }
        return $this;
    }
}

?>
