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
class SD_AdvancedAttributes_Manage_FiltersController extends Mage_Adminhtml_Controller_Action {
    
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
        $this->displayTitle('Manage Filters');

        $this->_initAction()
                ->renderLayout();
    }
    
    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('advancedattributes/advancedattributes')->load($id);
        
        if($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancedattributes')->__('Attribute does not exist'));
                $this->_redirect('*/*/');
            } 
        } else {
            $id = $this->getRequest()->getParam('attribute_id');
            $model->loadFromAttributeId($id);
        }
       
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            
            $model->setData($data);
        }
        
        Mage::register('advancedattributes_data', $model);
        
        $this->loadLayout();
        $this->_setActiveMenu('advancedattributes/filters');
        $this->displayTitle('Edit Filter Properties');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('advancedattributes/manage_filters_edit'))
                ->_addLeft($this->getLayout()->createBlock('advancedattributes/manage_filters_edit_tabs'));;


        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        $this->renderLayout();

    }
    
    public function newAction() {
        $filters = Mage::getModel('advancedattributes/advancedattributes')->getCollection();
        $filters->addAttributeIdFilter();
        $completedSuccessfully = true;
        $filtersLoaded = 0;
        
        foreach ($filters as $filter) {
            if($filter->getAttributeId() != '') {
                $model = Mage::getModel('advancedattributes/advancedattributes');
                $model->setData('attribute_id', $filter->getAttributeId());
                $model->setData('attribute_code', $filter->getAttributeCode());

                try {
                    $model->save();
                    $filtersLoaded++;
                } catch (Exception $e) {
                    $completedSuccessfully = false;
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    break;
                }
            }
        }
        
        if($completedSuccessfully) {
            if($filtersLoaded == 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('There were no new filters to load.'));
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The filters were successfully loaded'));
            }
        }
        
        $this->_redirect('*/*/index');
    }
    
    public function saveAction() {

        if ($data = $this->getRequest()->getPost()) {
            
            $model = Mage::getModel('advancedattributes/advancedattributes');

            $model->setData($data);

            if (!$this->getRequest()->getParam('id')) {
                $model->setData('attribute_id', $this->getRequest()->getParam('attribute_id'));
                $model->setData('attribute_code', $this->getRequest()->getParam('attribute_code'));
            }
                
            $requestId = $this->getRequest()->getParam('attribute_id');
            
            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The filter was successfully saved'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'attribute_id' => $requestId ));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                // redirect back to edit form
		if ($id = $this->getRequest()->getParam('id')) {
                    $this->_redirect('*/*/edit', array('id' => $id));
		} else {
		    $attribute_id      = $this->getRequest()->getParam('attribute_id');
                    $attribute_code      = $this->getRequest()->getParam('attribute_code');
	            $this->_redirect('*/*/edit', array(
	                	'attribute_id' => $attribute_id,
                                'attribute_code' => $attribute_code
	             ));
		}
                return;
            }
        } 
    }
    
    public function optionsAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('options.grid')
            ->setOptions($this->getRequest()->getPost('option_id', null));
        $this->renderLayout();
    }
    
    public function optionsgridAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('options.grid')
        ->setOptions($this->getRequest()->getPost('option_id', null));
        $this->renderLayout();
    }
    
    public function optionsEditAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('options.grid')
        ->setOptions($this->getRequest()->getPost('option_id', null));
        $this->renderLayout();
    }
    
    
    protected function displayTitle($data = null, $root = 'Advanced Filters') {

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
                $this->_title($this->__('Advanced Filters'))->_title($root);
            }
        }
        return $this;
    }
}

?>
