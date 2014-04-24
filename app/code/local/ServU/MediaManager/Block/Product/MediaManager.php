<?php

//Mage_Catalog_Block_Product_View_Media
//class ServU_MediaManager_Block_MediaManager extends Mage_Catalog_Block_Product_View_Abstract
class ServU_MediaManager_Block_Product_MediaManager extends Mage_Catalog_Block_Product_View_Media{

    protected function _getProductsFiles(){
        $product_id = Mage::registry('current_product')->getId();
        $collection = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->addAttributeToFilter('entity_id', $product_id);
        
        $file_collection = Mage::getModel('mediamanager/browse')->loadFileCollectionByProductCollection($collection);
//ORIGINAL CODE        
//        $collection  = Mage::getModel('mediamanager/products')->getCollection()
//                ->addFieldToFilter('product_id',$product_id);
//        $file_collection = Mage::getModel('mediamanager/browse')->getProductsFileCollection($collection);
        
        return $file_collection;
    }
}