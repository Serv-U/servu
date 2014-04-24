<?php
/**
 * @desc BlogProducts Controller
 * @author andrewprendergast
 */
class ServU_BlogProducts_Adminhtml_BlogproductsController extends Mage_Adminhtml_Controller_Action {
    
    public function productsAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('products.grid')
            ->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }
    
    public function productsgridAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('products.grid')
            ->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }    
}
?>