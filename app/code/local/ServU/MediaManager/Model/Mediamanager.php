<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mediamanager Model
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Model_Mediamanager extends Mage_Core_Model_Abstract 
{
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('mediamanager/mediamanager');
    }

    public function delete($fileId = null)
    {
        //Remove File without Exception
//        $file_path = Mage::helper('mediamanager/data')->getAbsoluteFilePath($fileId);
//        if(file_exists($file_path)){
//            unlink($file_path);
//        }
        
        $this->_getResource()->beginTransaction();
        try {
            //Remove File with Exception
            $this->removeFile($fileId);
            
            //Delete file record from database
            $this->_beforeDelete();
            $this->_getResource()->delete($this);
            $this->_afterDelete();

            $this->_getResource()->commit();
            $this->_afterDeleteCommit();

            //Delete product relationships from database
            Mage::getModel('mediamanager/productfiles')->removeProductFileById($fileId);
        }
        catch (Exception $e){
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }
    
    public function removeFile($fileId)
    {
        $file_extension = $this->load($fileId)->getData('file_extension');

        if($file_extension != 'url' && $file_extension != ''){
            $file_path = Mage::helper('mediamanager/data')->getAbsoluteFilePath($fileId);
            if(file_exists($file_path)){ 
                if(!unlink($file_path)){
                    throw new Exception('Unable to remove file(s).');
                }
            }
        }        
    }

    public function uploadFile($path, $file)
    {
        $uploader = new Varien_File_Uploader('upload_file');
        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','zip','pdf','doc','docx','odt','xls','xlsx','ods','csv','rtf','rar','txt'));
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $uploader->save($path, $file);
        return $uploader->getUploadedFileName();
    }
  
}
?>