<?php
/**
 * @desc Description of BlogProducts Model
 * @author andrewprendergast
 */
class ServU_BlogProducts_Model_Blogproducts extends Mage_Core_Model_Abstract {
    
    public function _construct() {
        parent::_construct();
        $this->_init('blogproducts/blogproducts');
    }
    
    /**
     * @desc Gets blog post's product collection based on blog's post_id
     * @param int $post_id
     * @return object
     */
    public function getBlogCollection($post_id){
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('quantity_description')
                ->addAttributeToSelect('small_image');
        
        $collection->getSelect()
                    ->join(array('bp' => 'servu_blogproducts'),
                        'e.entity_id = bp.product_id',
                        array('blog_id'))
                    ->where('bp.blog_id = '.$post_id);

        return $collection;
    }
    
    /**
     * @desc Clear old product-blog relationship entries from db and repopulate with newly selected products
     * @param int $blog_id
     * @param array $product_ids
     */
    public function setBlogProductRelationships($blog_id, $product_ids){
        $old_products = Mage::getModel('blogproducts/blogproducts')->getCollection()->addFieldToFilter('blog_id', $blog_id);
        $oldArray = array();
        foreach ($old_products as $old_product){
            $oldArray[] = $old_product->getData('product_id');
        }
        $newArray = array();
        foreach ($product_ids as $array_id => $new_product){
            $newArray[] = $array_id;
        }
        
        //Compare ids that need to be added/removed
        $older = array_diff($oldArray, $newArray);
        $newer = array_diff($newArray, $oldArray);
        
        //DEBUG
        //Mage::log(print_r($older, true), null, 'older.txt');
        //Mage::log(print_r($newer, true), null, 'newer.txt');
        
        //Save changes
        $this->_removeBlogProducts($blog_id, $older);
        $this->_setBlogProducts($blog_id, $newer);
    }
    
    private function _removeBlogProducts($blog_id, $products){
        foreach ($products as $key => $product_id){
            $old_id = $this->getCollection()->addFieldToFilter('product_id', $product_id)->addFieldToFilter('blog_id', $blog_id)->getFirstItem();
            $old_id->delete();
        }
    }
    
    private function _setBlogProducts($blog_id, $products){
        foreach ($products as $key => $product_id){
            $model = Mage::getModel('blogproducts/blogproducts');
            $model->setData('blog_id', $blog_id);
            $model->setData('product_id', $product_id);
            $model->save();
        }
    }
    
     /**
     * @desc Delete product-blog relationship entries from db based on specific blog_id
     * @param int $blog_id
     */
    public function deleteRelationshipsByBlogId($blog_id){
        $product_ids = $this->getCollection()
            ->addFieldToFilter('blog_id', $blog_id);
        $products_model = Mage::getModel('blogproducts/blogproducts');
        foreach ($product_ids as $array_id => $delete_ids){
            $products_model->setId($array_id)->delete();
        }
    }
}
?>