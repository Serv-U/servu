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
class SD_AdvancedAttributes_Manage_ConfigurablesController extends Mage_Adminhtml_Controller_Action {
    
    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/advancedattributes/configurables');
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('advancedattributes/configurables');

        return $this;
    }

    public function indexAction() {    
        $this->displayTitle('Manage Configurables');
        
        $this->_initAction()
                ->renderLayout();
    }
    
    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('advancedattributes/configurables')->load($id);
        
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
        $this->_setActiveMenu('advancedattributes/configurables');
        $this->displayTitle('Edit Configurable Properties');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('advancedattributes/manage_configurables_edit'));

        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        $this->renderLayout();

    }
    
    public function newAction() {
        $configurables = Mage::getModel('advancedattributes/configurables')->getCollection();
        $configurables->addAttributeIdFilter();
        $completedSuccessfully = true;
        $configurablesLoaded = 0;
        
        foreach ($configurables as $configurable) {
            if($configurable->getAttributeId() != '') {
                $model = Mage::getModel('advancedattributes/configurables');
                $model->setData('attribute_id', $configurable->getAttributeId());
                $model->setData('attribute_code', $configurable->getAttributeCode());

                try {
                    $model->save();
                    $configurablesLoaded++;
                } catch (Exception $e) {
                    $completedSuccessfully = false;
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    break;
                }
            }
        }
        
        if($completedSuccessfully) {
            if($configurablesLoaded == 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('There were no new filters to load.'));
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The filters were successfully loaded'));
            }
        }
        
        $this->_redirect('*/*/index');
    }
    
    public function saveAction() {
        
        if ($data = $this->getRequest()->getPost()) {
 
            $path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'attributes' . DS . 'configurables'. DS .'view' . DS;
			$resizedPath = $path . 'resized' . DS;
            $models = Array();
            $model = Mage::getModel('advancedattributes/configurableOptions');
            
            foreach($data as $key => $value) {
                 
                if(preg_match('/table_id/', $key)) {
                    if($value == '') {
                        $value =  str_replace('table_id_', '', $key);
                    }
                    $model->setData('id', $value);
                }
                
                if(preg_match('/option_id/', $key)){
                    if($value == '') {
                        $value =  str_replace('option_id_', '', $key);
                        $optionId = $key;
                    }

                    if(isset($_FILES['product_view_image_'.$value]['name']) && $_FILES['product_view_image_'.$value]['name'] != '') {
                        $uploaded = false;
                        try {
                            //try to make the upload
                            $uploader = new Varien_File_Uploader('product_view_image_'.$value);
                            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                            $uploader->setAllowRenameFiles(false);
                            $uploader->setFilesDispersion(false);
                            $uploader->save($path);
								 
                            if (($uploaded = $uploader->getUploadedFileName()) > '') {
                                $model->setData('product_view_image', $uploaded);
                            }
							
							if (!file_exists($resizedPath.$uploaded) && file_exists($path.$uploaded)){
								$imageObj = new Varien_Image($path.$uploaded);
								$imageObj->constrainOnly(TRUE);
								$imageObj->keepAspectRatio(TRUE);
								$imageObj->keepFrame(FALSE);
								$imageObj->resize(30, 30);
								$imageObj->save($resizedPath.$uploaded);	
							}
                        } catch (Exception $e) {

                        }
                    }
                    elseif ($data['product_view_image_'.$value]['delete'] == 1) {
                        $model->setData('product_view_image', '');
                    }
                    $model->setData('option_id', $value);
                }

                if(preg_match('/option_label/', $key)) {
                    $model->setData('option_label', $value);
                    array_push($models, $model);
                    $model = Mage::getModel('advancedattributes/configurableOptions');
                } 
            }

            // try to save it
            try {
                // save the data
                foreach($models as $model) {
                    $model->save();
                }

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The option was successfully saved'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
               
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/manage_configurables/edit', array('id' => $this->getRequest()->getParam('id'), 'attribute_id' => $this->getRequest()->getParam('attribute_id')));
                    return;
                }
                // go to grid
                $this->_redirect('*/manage_configurables/index');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/manage_configurables/index');
                
                return;
            }
        }
  
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
