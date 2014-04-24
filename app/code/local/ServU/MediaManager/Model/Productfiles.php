<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductFiles
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Model_Productfiles extends Mage_Core_Model_Abstract{
    
    public function _construct(){
        parent::_construct();
        $this->_init('mediamanager/productfiles');
    }
    
    public function setProductsFiles($new_product_ids){
        $product_id = Mage::app()->getRequest()->getParam('id');

        $old_files = $this->getCollection()->addFieldToFilter('product_id', $product_id);
        $oldArray = array();
        foreach ($old_files as $old_file){
            //Mage::log($old_file->getData('file_id'), null, 'oldids.txt');  
            $oldArray[] = $old_file->getData('file_id');
        }

        foreach ($new_product_ids as $array_id => $new_prod){
            //Mage::log($array_id, null, 'newids.txt');
            $newArray[] = $array_id;
        }
        
        //Compare ids that need to be added/removed
        $older = array_diff($oldArray, $newArray);
        $newer = array_diff($newArray, $oldArray);

        //DEBUG
//        Mage::log(print_r($older, true), null, 'older.txt');
//        Mage::log(print_r($newer, true), null, 'newer.txt');

        //Save changes
        $this->_removeProductFiles($product_id, $older);
        $this->_setProductFiles($product_id, $newer);
    }
    
    private function _removeProductFiles($product_id, $files){
        foreach ($files as $key => $file_id){
            $old_id = $this->getCollection()->addFieldToFilter('file_id', $file_id)->addFieldToFilter('product_id', $product_id)->getFirstItem();
            $old_id->delete();
        }
    }
    
    private function _setProductFiles($product_id, $files){
        foreach ($files as $key => $file_id){
            $model = Mage::getModel('mediamanager/products');
            $model->setData('file_id', $file_id);
            $model->setData('product_id', $product_id);
            $model->save();
        }
    }
    
    public function removeProductFileById($file_id){
        $productfiles = $this->getCollection()
            ->addFieldToFilter('file_id', $file_id);
        $productfiles_model = Mage::getModel('mediamanager/productfiles');
        foreach ($productfiles as $array_id => $old_ids){
            $productfiles_model->setId($array_id)->delete();
        }
    }
}
?>
