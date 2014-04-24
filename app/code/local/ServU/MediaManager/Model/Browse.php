<?php
/**
 * Description of Browse Model
 * @author dustinmiller
 */
class ServU_MediaManager_Model_Browse extends ServU_MediaManager_Model_Mediamanager {

    /**
     * @desc Get name of manufacturer from manufacturer's id
     * @param int $id
     * @return string $manufacturer
     */
    public function getManufacturerName($id) {
        $attribute = Mage::getModel('eav/entity_attribute')
                ->loadByCode('catalog_product', 'manufacturer');
        
        $manufacturers = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attribute->getData('attribute_id'))
                ->setStoreFilter(0, false);

        foreach($manufacturers as $manufacturer){
            if($manufacturer->getOptionId() == $id){
                return $manufacturer->getValue();
            }
        }

    }
    
    /**
     * @desc Get collection of Manufacturers
     * @return object $collection 
     */
    public function getManufacturers() {
        $attribute = Mage::getModel('eav/entity_attribute')
                ->loadByCode('catalog_product', 'manufacturer');

        $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attribute->getData('attribute_id'))
                ->setStoreFilter(0, false);

        return $collection;
    }
    
    /**
     *
     * @param object $request
     * @return collection
     */
    public function filterResults($request){
        $type = $request->getParam('type');
        $id = $request->getParam('id');
        
        //Filter collection by group
        switch($request->getActionName()){
            case 'cat':
                $collection = $this->_browseByCategory($id);
                break;
            case 'man':
                $collection = $this->_browseByManufacturer($id);
                break;
            case 'med':
                $collection = $this->_browseByMediaType($type);
                break;
            default:
                //Get all files as default collection
                $collection = Mage::getModel('mediamanager/browse')->getCollection()
                                ->addFieldToFilter('file_status',1)
                                //Order files by most recently created...
                                //->setOrder('id', 'desc');
                                //Order files alphabetically...
                                ->setOrder('file_title', 'asc');                
                break;
        }
        
        //Filter by search string
        if($searchText = $request->getParam('mediaManagerSearchText')) {
            $searchIds = Mage::getModel('mediamanager/search')->searchFiles($searchText, $request->getParam('mediaManagerSearchType'));
            $collection->addFieldToFilter('id', array('in' => $searchIds));
        }
        
        return $collection;
    }

    /**
     * Get file collection for specified file type
     * @param string $filetype
     * @return object $collection 
     */
    protected function _browseByMediaType($filetype) {
        $extensions = Mage::helper('mediamanager/data')->getMediaTypeExtensions($filetype);
        
        $collection = $this->getCollection()
                ->addFieldToFilter('file_extension', array('in' => $extensions))
                ->setOrder('file_title', 'asc');
        
        return $collection;
    }
    
    /**
     * @desc Get file collection for category
     * @param int $id
     * @param object $collection
     * @return collection
     */
    protected function _browseByCategory($id) {
        //Retrieve Catalog Product Collection based on Category
        $category = Mage::getModel('catalog/category')->load($id);
        $catalogCollection = Mage::getModel('catalog/product')
                                ->getCollection()
                                ->addAttributeToSelect('entity_id')
                                ->addCategoryFilter($category);

        //Load file collection
        return $this->loadFileCollectionByProductCollection($catalogCollection);
    }
    
    /**
     * Retrieve File Collection based on catalog/product collection
     * @param array $collection
     * @return array
     */
    public function loadFileCollectionByProductCollection($collection){
        //Only retrieve enabled and visible products
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection); 
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
        //Get array of product ids
        $catalogProductIds = $collection->getAllIds();
        
        //Get array of Media Manager file ids
        $mmCollection = Mage::getModel('mediamanager/products')
                            ->getCollection()
                            ->addFieldToFilter('product_id', array( 'in' => $catalogProductIds));
        //Create array manually since getAllIds() gets table's id (not file id)
        $mmConnectionIds = array();
        foreach($mmCollection as $mm){
            $mmConnectionIds[] = $mm->getData('file_id');
        }
        
        //Return file collection
        return $this->_loadFileCollectionByIds($mmConnectionIds);
    }

    
    /**
     * @desc Retrieve file collection from an array of file ids
     * @param array $file_ids
     * @return object $collection 
     */
    protected function _loadFileCollectionByIds($file_ids) {
        $collection = array();
        
        if(!empty($file_ids)){
            $collection = $this->getCollection()
                    ->addFieldToFilter('id', array('in' => $file_ids))
                    ->addFieldToFilter('file_status',1)
                    ->setOrder('file_title', 'asc');
        }
        
        return $collection;
    }
    
    /**
     * @desc Get collection of files based on manufacture id
     * @param int $id
     * @return object $collection 
     */
    protected function _browseByManufacturer($id) {
//        $catalogCollection = Mage::getModel('catalog/product')
//                                ->getCollection()
//                                ->addAttributeToSelect('entity_id')
//                                ->addAttributeToFilter('manufacturer', $id);
////        Mage::log(print_r($catalogCollection->getAllIds(), true));
//
//        //Load file collection
//        return $this->loadFileCollectionByProductCollection($catalogCollection);
        
//THIS CODE RUNS FASTER 4-5s FASTER THAN ABOVE STREAMLINED CODE??? Only 200 some items byMan renders no faster than 10s, but byCat with furniture with over 80,000+ renders in 3s??
        //Get all unique product ids from Media Manager's product table
        $mmProductIds = $this->_getMediaManagerProductIds();
        
        //Get collections of Media Manager products
        $catalogCollection = Mage::getModel('catalog/product')
                                ->getCollection()
                                ->addAttributeToSelect('entity_id')
                                ->addAttributeToFilter('entity_id', array( 'in' => $mmProductIds))
                                ->addAttributeToFilter('manufacturer', $id);

        //Get file collection from product ids
        $collection = $this->_getMediaManagerFileCollection($catalogCollection);
        
        return $collection;        
    }
    
     /**
     * @desc Get file collection based on entity ids from catalog collection
     * @param object $catalogCollection
     * @return object $collection
     */
    private function _getMediaManagerFileCollection($catalogCollection){
        $collection = array();
        
        $file_ids = array();
        foreach($catalogCollection as $product){
            $productCollection = Mage::getModel('mediamanager/products')->getCollection()
                    ->addFieldToFilter('product_id',$product->entity_id);
            foreach($productCollection as $product){
                $file_ids[] = $product->file_id;
            }
        }
        
        //Load file collection based on array of file ids
        if(!empty($file_ids)){
            $collection = $this->_loadFileCollectionByIds($file_ids);
        }
        
        return $collection;
    }
    
    
     /**
     * @desc Get unique product ids from Media Manager's product table
     * @return array $productIds
     */
    private function _getMediaManagerProductIds(){
        $productIds = array();
        
        $productCollection = Mage::getModel('mediamanager/products')
                ->getCollection()
                ->addFieldToSelect('product_id')
                ->distinct(true)
                ->addOrder('product_id', 'asc');
        
        foreach($productCollection as $product){
            $productIds[] = $product->getProduct_id();
        }
        
        return $productIds;
    }
}
?>