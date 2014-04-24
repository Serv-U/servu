<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Products
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Model_Products extends Mage_Core_Model_Abstract{
    
    public function _construct(){
        parent::_construct();
        $this->_init('mediamanager/products');
    }
    
    public function saveFilesProducts($file_id, $links){
        $old_products = $this->getCollection()->addFieldToFilter('file_id', $file_id);
        $oldArray = array();
        foreach ($old_products as $old_product){
            //Mage::log($old_product->getData('product_id'), null, 'oldids.txt');  
            $oldArray[] = $old_product->getData('product_id');
        }
        
        $newArray = array();
        if($product_ids = $links['products']){
            $new_product_ids = Mage::helper('adminhtml/js')->decodeGridSerializedInput($product_ids);
            
            foreach ($new_product_ids as $array_id => $new_field){
                //Mage::log($array_id, null, 'newids.txt');  
                $newArray[] = $array_id;
            }            
        }
        
        //Compare ids that need to be added/removed
        $older = array_diff($oldArray, $newArray);
        $newer = array_diff($newArray, $oldArray);
        
        //DEBUG
        //Mage::log(print_r($older, true), null, 'older.txt');        
        //Mage::log(print_r($newer, true), null, 'newer.txt');        
        
        //Save changes
        $this->_removeFilesProducts($file_id, $older);
        $this->_setFilesProducts($file_id, $newer);
    }    
    
    private function _removeFilesProducts($file_id, $products){
        foreach ($products as $key => $product_id){
            $old_id = $this->getCollection()->addFieldToFilter('file_id', $file_id)->addFieldToFilter('product_id', $product_id)->getFirstItem();
            $this->setId($old_id->getData('id'))->delete();
            //Doesn't work???
            //$old_id->delete();
        }
    }
    
    private function _setFilesProducts($file_id, $products){
        foreach ($products as $key => $product_id){
            $model = Mage::getModel('mediamanager/products');
            $model->setData('file_id', $file_id);
            $model->setData('product_id', $product_id);
            $model->save();
        }
    }
}

?>