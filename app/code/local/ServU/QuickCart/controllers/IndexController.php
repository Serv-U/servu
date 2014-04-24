<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of QuickCart Index
 *
 * @author andrewprendergast
 */
class ServU_QuickCart_IndexController extends Mage_Core_Controller_Front_Action
{
    
    public function indexAction () {
        //Restrict direct access to page
        if( !isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ) ){
            Mage::log('QuickCart\'s Index Ajax page was accessed directly.');
            $this->_redirect('checkout/cart');
            return false;
        }

        //Save products to cart
        $model = Mage::getModel('quickcart/QuickCart');
        $model->setData('postdata', $this->getRequest()->getPost() );
        $data = $model->addToCart();
        
        //Get cart sidebar block
        //$this->loadLayout();
        $data['sidebar_cart'] = $this->getLayout()->createBlock('checkout/cart_sidebar')->setTemplate('checkout/cart/sidebar.phtml')->toHtml();
/*
        //Rebuild shopping cart block if user is on shopping cart page
        $page = $this->getRequest()->getPost('currentURL');
        if (strpos($page, "checkout/cart") !== false) {
            $data['checkout_types'] = $this->getLayout()->createBlock('checkout/onepage_link')->setTemplate('checkout/onepage/link.phtml')->toHtml();
            $data['cart_page_body'] = $this->getLayout()->createBlock('checkout/cart')->setTemplate('checkout/cart.phtml')->toHtml();
            $data['cart_page_totals'] = $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();
            $data['shipping_coupon'] = $this->getLayout()->createBlock('checkout/cart_coupon')->setTemplate('checkout/cart/coupon.phtml')->toHtml();
            $data['shipping_coupon'] .= $this->getLayout()->createBlock('checkout/cart_shipping')->setTemplate('checkout/cart/shipping.phtml')->toHtml();
        }
*/
        echo json_encode($data);
        exit();
    }
    
    public function nextAttributeAction () {
        //Restrict direct access to page
        if( !isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ) ){
            Mage::log('QuickCart\'s nextAttribute Ajax page was accessed directly.');
            $this->_redirect('checkout/cart');
            return false;
        }
        
        //Retrieve dropdown for next attribute
        $model = Mage::getModel('quickcart/QuickCart');
        $model->setData('postdata', $this->getRequest()->getPost() );
        $data = $model->getNextAttribute();
        
        echo json_encode($data);
        exit();
    }    
}
?>