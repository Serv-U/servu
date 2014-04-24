<?php
/**
 * Description of Search Model
 * @author dustinmiller
 */
class ServU_MediaManager_Model_Search extends ServU_MediaManager_Model_Browse {
    
    public function searchFiles($searchText, $searchType) {
        //Format search string
        $searchText = $this->_cleanSearchText($searchText);

//        //Require minimum number of characters
//        if(strlen($searchText) <= 4){
//            Mage::getSingleton('core/session')->addError('Please specify a search term longer than four characters.');
//            return null;
//        }
         
        //Apply Search
        $file_ids = array();
        switch($searchType){
            //SEARCH SKUs
            case 'sku':
                $file_ids = $this->_searchSKU($searchText);
                break;
            //SEARCH MANUFACTURER NUMBERS
            case 'manufacturer':
                $file_ids = $this->_searchManufacturerNumbers($searchText);
                break;
            //SEARCH DOCUMENT NAME OR TITLE...
            case 'filename':
                $file_ids = $this->_searchFileName($searchText);
                break;
        }

        return $file_ids;
    }
        
    protected function _cleanSearchText($searchText){
        $searchText = trim($searchText);
        //Strip out special characters - Catalog lists some items with * (see jrcq-182*)
        $replace = array('*','#','^','=');
        $searchText = str_replace($replace, '', $searchText);
        
        return $searchText;
    }

    protected function _searchFileName($searchText){
        return Mage::getModel('mediamanager/mediamanager')
            ->getCollection()
            ->addFieldToSelect('id')
            ->addFieldToFilter(
                array('file_title','file_name'),
                array(
                    array('like'=>'%'.$searchText.'%'),
                    array('like'=>'%'.$searchText.'%')
                )
            )
            ->getAllIds();
    }
    
    protected function _searchManufacturerNumbers($searchText) {
        //SEARCH EXACT MATCHES
        $products = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToFilter('manufacturer_sku', $searchText);
        
        //SEARCH PARTIALS
        if($products->getSize() == 0){
            $products = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addAttributeToFilter('manufacturer_sku', array('like' => '%'.$searchText.'%'));
        }
        
        //PROCESS RESULTS
        $file_ids = array();
        $collection  = Mage::getModel('mediamanager/products')
                ->getCollection()
                ->addFieldToFilter('product_id', array('in' => $products->getAllIds()));
        foreach($collection as $product){
            $file_ids[] = $product->getData('file_id');
        }
        
        return $file_ids;
    }

    protected function _searchSKU($searchText){
        //SEARCH FOR EXACT SKU
        if($product = Mage::getModel('catalog/product')->loadByAttribute('sku',$searchText)){
            $entity_id = $product->getEntityId();
            $collection  = Mage::getModel('mediamanager/products')->getCollection()
                ->addFieldToFilter('product_id',$entity_id);
            foreach($collection as $product){
                $file_ids[] = $product->getData('file_id');
            }
        }
        //SEARCH PARTIAL SKUS (ie: jrcq-182 and lavq-7322)
        else{
            $file_ids = $this->_checkPartialSkus($searchText);
        }
        return $file_ids;
    }
    
    protected function _checkPartialSkus($sku){
        //Load possible skus
        if($partials = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('sku', array('like' => '%'.$sku.'%'))){
            foreach($partials as $partial){
                //Do not display configurable items
                if(!preg_match("/\^|\=|\#|\*/",$partial->sku)){
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$partial->sku);
                    $productCollection = Mage::getModel('mediamanager/products')
                        ->getCollection()
                        ->addFieldToFilter('product_id',$product->getEntityId());
                    foreach($productCollection as $product){
                        $file_ids[] = $product->file_id;
                    }
                }
            }

            if(!empty($file_ids)){ return $file_ids; }
        }

        return false;
    }
}
?>