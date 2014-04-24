<?php
/**
 * @desc ProductBlogs Controller
 * @author andrewprendergast
 */
class ServU_BlogProducts_Adminhtml_ProductblogsController extends Mage_Adminhtml_Controller_Action {
    
    public function productblogsAction() {
//        $this->_initProduct();
        $this->loadLayout();
//        $this->getLayout()->getBlock('products.grid')
//            ->setProducts($this->getRequest()->getPost('products', null));

//        $this->getLayout()->getBlock('catalog.product.edit.tab.related')
//            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }
    
    public function productblogsGridAction() {
//        $this->_initProduct();
        $this->loadLayout();
//        $this->getLayout()->getBlock('products.grid')
//            ->setProducts($this->getRequest()->getPost('products', null));

//        $this->getLayout()->getBlock('catalog.product.edit.tab.related')
//            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }
}
?>