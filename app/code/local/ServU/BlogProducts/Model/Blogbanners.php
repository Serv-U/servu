<?php
/**
 * @desc Description of BlogBanners Model
 * @author andrewprendergast
 */
class ServU_BlogProducts_Model_Blogbanners extends Mage_Core_Model_Abstract {
    
    public function _construct() {
        parent::_construct();
        $this->_init('blogproducts/blogbanners');
    }
    
    public function saveBanner($blog_id, $banner){
        if($this->getCollection()->addFieldToFilter('blog_id', $blog_id)->getSize() > 0){
            $this->getCollection()->addFieldToFilter('blog_id', $blog_id)->getFirstItem()->setData('banner', $banner)->save();
        } else {
            Mage::getModel('blogproducts/blogbanners')
                ->setData('blog_id', $blog_id)
                ->setData('banner', $banner)
                ->save();
        }
    }
    
    public function getBanner($blog_id){
        $banner = $this->getCollection()->addFieldToFilter('blog_id', $blog_id)->getFirstItem();
        return $banner->getData('banner');
    }
}
?>