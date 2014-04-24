<?php
/**
 * Description of ProductBlogs Model
 * @author andrewprendergast
 */
class ServU_BlogProducts_Model_Productblogs extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('blogproducts/productblogs');
    }

    public function getBlogCollectionByProductId($product_id){
        $blogs = Mage::getModel('blogproducts/productblogs')->getCollection()->addFieldToFilter('product_id',$product_id);

        foreach($blogs as $blog){
            $blog_id = $blog->getData('blog_id');
            $post = Mage::getmodel('blog/blog')->getCollection()->addFieldToFilter('post_id',$blog_id)->getFirstItem();

            $blog->setData('url', Mage::getBaseUrl() . 'blog/' . $post->getData('identifier'));
            $blog->setData('title',$post->getData('title'));
        }
        return $blogs;
    }
    
    public function setProductBlogRelationships($product_id, $blog_ids){
        $old_blogs = Mage::getModel('blogproducts/productblogs')->getCollection()->addFieldToFilter('product_id', $product_id);
        $oldArray = array();
        foreach ($old_blogs as $old_blog){
            $oldArray[] = $old_blog->getData('blog_id');
        }
        $newArray = array();
        foreach ($blog_ids as $array_id => $new_blog){
            $newArray[] = $array_id;
        }
        
        //Compare ids that need to be added/removed
        $older = array_diff($oldArray, $newArray);
        $newer = array_diff($newArray, $oldArray);
        
        //DEBUG
        //Mage::log(print_r($older, true), null, 'older.txt');
        //Mage::log(print_r($newer, true), null, 'newer.txt');
        
        //Save changes
        $this->_removeProductBlogs($product_id, $older);
        $this->_setProductBlogs($product_id, $newer);
    }
    
    private function _removeProductBlogs($product_id, $blogs){
        foreach ($blogs as $key => $blog_id){
            $old_id = $this->getCollection()->addFieldToFilter('blog_id', $blog_id)->addFieldToFilter('product_id', $product_id)->getFirstItem();
            $old_id->delete();
        }
    }
    
    private function _setProductBlogs($product_id, $blogs){
        foreach ($blogs as $key => $blog_id){
            $model = Mage::getModel('blogproducts/productblogs');
            $model->setData('blog_id', $blog_id);
            $model->setData('product_id', $product_id);
            $model->save();
        }
    }
}
?>