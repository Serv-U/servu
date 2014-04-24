<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Files
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Adminhtml_FilesController extends Mage_Adminhtml_Controller_Action {

    public function productsAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('products.grid')
            ->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }
    
    public function productsgridAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('products.grid')
            ->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }
    
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('mediamanager/files');

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
        ->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('mediamanager/mediamanager')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('mediamanager_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('mediamanager/files');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()
                    ->createBlock('mediamanager/adminhtml_files_edit'))
                    ->_addLeft($this->getLayout()
                    ->createBlock('mediamanager/adminhtml_files_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('manager')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    public function newAction(){
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('mediamanager/mediamanager');
            $model->setData($data);

            $fileId = $this->getRequest()->getParam('id');
            if (!$this->getRequest()->getParam('id')) {
                $model->setData('file_id', $this->getRequest()->getParam('file_id'));
                $fileId = $this->getRequest()->getParam('file_id');
            }
            
            //Try to save file/url
            try {
                $file_type = $this->getRequest()->getPost('file_type');
                
                //Save Embedded Video
                if($file_type == 1) {
                    if($file_name = $this->getRequest()->getPost('embed_video')){
                        $file_name = Mage::helper('mediamanager')->formatURL($file_name);
                        $model->setData('file_extension','video');
                        $model->setData('file_name',$file_name);
                        $model->setData('file_size','0');
                    }
                }
                //Save External Url
                elseif($file_type == 2) {
                    if($file_name = $this->getRequest()->getPost('external_url')){
                        $file_name = Mage::helper('mediamanager')->formatURL($file_name);
                        $model->setData('file_extension','url');
                        $model->setData('file_name',$file_name);
                        $model->setData('file_size','0');
                    }
                }
                //Save Uploaded file
                elseif(!empty($_FILES['upload_file']) && file_exists($_FILES['upload_file']['tmp_name']) && $file = $_FILES['upload_file']['name']){
                    //Remove previous file
                    $model->removeFile($fileId);

                    //Get file extension and set path
                    $file_extension = pathinfo($file);
                    $file_extension = strtolower($file_extension['extension']);
                    $extension_folder = Mage::helper('mediamanager/data')->getFileFolder($file_extension);

                    //To display folder and extension instead...
                    //$model->setData('file_extension',$extension_folder . "/" . $file_extension);

                    //Check for and create MediaManager file
                    $path = Mage::getBaseDir('media'). DS . "MediaManager" . DS . $extension_folder . DS;
                    if (!file_exists($path)) { 
                        mkdir($path);
                    }

                    $file = strtolower($file);
                    $file_name = $model->uploadFile($path, $file);

                    $model->setData('file_extension',$file_extension);
                    $model->setData('file_name',$file_name);
                    $model->setData('file_size', filesize($path . $file_name));

                    //Display warning if file had to be renamed
                    if($file_name != $_FILES['upload_file']['name']){
                        $this->_getSession()->addWarning($this->__('Please note that a file with the same name already exists or your original filename needed to be modified. Your file has been renamed to \''.$file_name.'\'.'));
                    }
                }
                
                //Set Modified Time
                $now = Mage::getModel('core/date')->timestamp(time());
                $model->setData('date_modified', date('Y-m-d H:i:s', $now));
                
                $model->save();

                //Save related products only if products tab was loaded
                if($links = $this->getRequest()->getPost('links')){
                    Mage::getmodel('mediamanager/products')->saveFilesProducts($model->getId(), $links);
                }
                
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The file was successfully saved'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                //Display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                //Save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                //Redirect back to edit form
		if ($id = $this->getRequest()->getParam('id')) {
                    $this->_redirect('*/*/edit', array('id' => $id));
		} else {
		    $file_id = $this->getRequest()->getParam('file_id');
	            $this->_redirect('*/*/edit', array('file_id' => $file_id));
		}
                return;
            }
        } 
    }

    public function deleteAction() {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('mediamanager/mediamanager');
                $model->load($id);
                //$title = $model->getName();
                $model->delete($id);
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('File was successfully deleted'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                //Mage::dispatchEvent('about-bsd-store', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find information to delete'));
        // go to grid
        $this->_redirect('*/*/');
    }
    
    public function massDeleteAction() {
        $fileIds = $this->getRequest()->getParam('mediamanager');
        $success = false;
        
        if (!is_array($fileIds)) {
            $this->_getSession()->addError($this->__('Please select file(s).'));
            $this->_redirect('*/*/index');
        }
        else {
            try {
                foreach($fileIds as $fileId) {
                    try{
                        $file = Mage::getSingleton('mediamanager/mediamanager')->load($fileId)->delete($fileId);
                        $success = true; 
                    } catch (Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }                        
                }
                if($success === true){
                    $this->_getSession()->addSuccess($this->__('The file(s) have been deleted.'));
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            $this->_redirect('*/*/');
        }
    }
    
    public function massDisableAction() {
        $fileIds = $this->getRequest()->getParam('mediamanager');

        if (!is_array($fileIds)) {
            $this->_getSession()->addError($this->__('Please select file(s).'));
            $this->_redirect('*/*/index');
        }
        else {
            try {
                foreach($fileIds as $fileId) {
                    try{
                        $file = Mage::getSingleton('mediamanager/mediamanager')->load($fileId);
                        $file->setData('file_status', 0);
                        $file->save();
                    } catch (Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }                        
                }
                $this->_getSession()->addSuccess($this->__('The file(s) have been disabled.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            $this->_redirect('*/*/');
        }
    }    
    
    public function massEnableAction()
    {
        $fileIds = $this->getRequest()->getParam('mediamanager');

        if (!is_array($fileIds)) {
            $this->_getSession()->addError($this->__('Please select file(s).'));
            $this->_redirect('*/*/index');
        }
        else {
            try {
                foreach($fileIds as $fileId) {
                    try{
                        $file = Mage::getSingleton('mediamanager/mediamanager')->load($fileId);
                        $file->setData('file_status', 1);
                        $file->save();
                    } catch (Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }                        
                }
                $this->_getSession()->addSuccess($this->__('The file(s) have been enabled.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            $this->_redirect('*/*/');
        }
    }    
    
    //Add link to view file with Google Doc Viewer
    public function viewAction(){
        $id = $this->getRequest()->getParam('id');
        $file_url = Mage::helper('mediamanager')->viewFileFromAdmin($id);
        header('Location: ' . $file_url);
        die;
    }
}
?>