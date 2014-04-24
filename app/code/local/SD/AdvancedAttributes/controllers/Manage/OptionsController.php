<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OptionsController
 *
 * @author dustinmiller
 */
class SD_AdvancedAttributes_Manage_OptionsController extends Mage_Adminhtml_Controller_Action {
    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/advancedattributes/filters');
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('advancedattributes/filters');

        return $this;
    }

    public function indexAction() {
        $this->displayTitle('Option Properties');


        $this->_initAction()
                ->renderLayout();
    }
    
    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('advancedattributes/options')->load($id);
        $model->setData('attribute_id', $this->getRequest()->getParam('attribute_id'));
        
        if($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancedattributes')->__('Option does not exist'));
                $this->_redirect('*/manage_filters/edit', array('id' => $model->getId()));
                    return;
            } 
        } else {
            $id = $this->getRequest()->getParam('option_id');
            $model->loadFromOptionId($id);
        }
       
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }
        
        $model->setData('attribute_id', $this->getRequest()->getParam('attribute_id'));
        
        Mage::register('advancedattributes_options_data', $model);
        
        $this->loadLayout();
        $this->_setActiveMenu('advancedattributes/filters');
        $this->displayTitle('Edit Options Properties');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('advancedattributes/manage_options_edit'));
        
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        $this->renderLayout();

    }
    
    public function saveAction() {

        if ($data = $this->getRequest()->getPost()) {
            $redirectBack   = $this->getRequest()->getParam('back', false);
            $model = Mage::getModel('advancedattributes/options');

            $model->setData($data);
            
            if (!$this->getRequest()->getParam('id')) {
                $model->setData('option_id', $this->getRequest()->getParam('option_id'));
                $model->setData('attribute_id', $this->getRequest()->getParam('attribute_id'));
            }

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The option was successfully saved'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
               
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/manage_options/edit', array('id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/manage_filters/index');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                // redirect back to edit form
		if ($id = $this->getRequest()->getParam('id')) {
                    $this->_redirect('*/manage_options/edit', array('id' => $id));
		} else {
		    $attribute_id      = $this->getRequest()->getParam('attribute_id');
                    $option_code      = $this->getRequest()->getParam('option_id');
	            $this->_redirect('*/*/edit', array(
	                	'attribute_id' => $attribute_id,
                                'option_id' => $option_id
	             ));
		}
                return;
            }
        } 
    }
    
    protected function displayTitle($data = null, $root = 'Options') {

        if (!Mage::helper('advancedattributes')->magentoVersion()) {
            if ($data) {
                if (!is_array($data)) {
                    $data = array($data);
                }
                foreach ($data as $title) {
                    $this->_title($this->__($title));
                }
                $this->_title($this->__($root));
            } else {
                $this->_title($this->__('Options'))->_title($root);
            }
        }
        return $this;
    }
}

?>
