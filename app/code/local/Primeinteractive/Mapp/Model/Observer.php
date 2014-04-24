<?php

class Primeinteractive_Mapp_Model_Observer extends Varien_Object
{
    public function applyCoupon(Varien_Event_Observer $observer) {
        $cart = Mage::getSingleton('checkout/cart')->getQuote();
        $coupon = strtolower(trim($cart->getCouponCode()));

        //Redirect if coupon code is for MAPP
        if(!empty($coupon) && preg_match('/^mapp/', $coupon)){
            //Set Message and Redirect
            $storeId = Mage::app()->getStore()->getStoreId();
            $url = Mage::helper('mapp')->buildCartUrl($coupon, $storeId);
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            Mage::app()->getResponse()->sendResponse();
            exit;
        }
    }
    
    public function validateCoupons(){
        //Validate all Mapp coupons
        Mage::getSingleton('core/session', array('name' => 'frontend'));            
        $session = Mage::getSingleton('checkout/session');
        $previous_coupons = $session->getMappCoupon();
        if($previous_coupons != ''){
            Mage::getModel('mapp/mapp')->modifyCart();
        }
    }
}