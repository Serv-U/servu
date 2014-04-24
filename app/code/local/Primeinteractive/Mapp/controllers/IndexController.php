<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * DISCLAIMER
 *
 *
 * @category   Primeinteractive
 * @package    Primeinteractive_Mapp
 * @version    1.0
 * @copyright   Copyright (c) 2012 Prime Interactive, Inc.
 */

class Primeinteractive_Mapp_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function processMappRequestAction() {
        //Mage::log("\n".__FILE__." (".__LINE__.")\n".__METHOD__."\n");
	$params = $this->getRequest()->getParams();
        
        //Validate email address
        if (!Zend_Validate::is($params['email'], 'EmailAddress')) {
            echo '<em><a href="#" onclick="showMappForm(); return false;">Invalid Email. Please enter a valid Email address.</a></em>';
            exit;
        }
        
        //Retrieve product data
        $data = $this->_getProductDataForMapp($params['productIdParam']);
        
        //Set Customer Email
        $data['emailid'] = trim($params['email']);

        //Set Customer Name
        $data['name'] = Mage::helper('mapp')->getCustomerName($data['emailid']);
        
        //Save data to database
        $mapp_id = $this->addMappRequest($data);

        //Send Email
        //Use a specific name processMappRequest instead of sendmail to avoid spam
	Mage::getModel('mapp/mapp')->sendMappEmail($mapp_id);
        
        //Display response for frontend 
        $_mappresponse = Mage::getStoreConfig('mapp_code/mapp_group/mapp_response');
        echo '<em>'.$_mappresponse.'</em>';
    }

    protected function _getProductDataForMapp($product_id){
        $data = array();
        
	//load product object from product id
	$_product = Mage::getModel('catalog/product')->load($product_id);

        //Get product data
        $data['mapprice'] = number_format($_product->getMapPrice(), 2, '.', '');
	$data['sku'] = trim($_product->getSku());
	$data['productname'] = $_product->getName();
	$data['producturl'] = $_product->getUrlInStore();
        
        return $data;
    }
    
    protected function addMappRequest($data) {
        //Set Store ID
        $data['store_id'] = Mage::app()->getStore()->getId();
        
        //Generate Random Coupon Code
        $data['coupon_code'] = Mage::helper('mapp/data')->generateCouponCode();
        
        //Set time created and updated
        $date = new DateTime();
        $data['created_time'] = $date->format('Y-m-d H:i:s');
        $data['update_time'] = $date->format('Y-m-d H:i:s');

	//Write to database
        $mapp = Mage::getModel('mapp/mapp');
        $mapp->setData($data);
        $mapp->save();
        
        //Return mapp_id to send email
        $item = Mage::getModel('mapp/mapp')->getCollection()->addFieldToFilter('coupon_code',$data['coupon_code'])->getFirstItem();
        return $item->getData('mapp_id');
        
        //DEBUG
        //Mage::log("\n"._FILE_." ("._LINE_.")\n"._METHOD_."\n".print_r($Query, true));
    }

    public function redirectToCartAction() {
	$cartParams = $this->getRequest()->getParams();
        
        if(!empty($cartParams['couponCode'])){
            //load product from coupon code
            $coupon = $cartParams['couponCode'];
            $mapp = Mage::getModel('mapp/mapp')->getCollection()->addFieldToFilter('coupon_code', $coupon);
            if($mapp->getSize() > 0){
                //Load coupon's product
                $sku = $mapp->getFirstItem()->getData('sku');
                $product = Mage::helper('catalog/product')->getProduct($sku, Mage::app()->getStore()->getId(), 'sku');

                //Add product to cart
                Mage::getModel('mapp/mapp')->addProductToCart($product);
            }

            //Add new coupon to session here so that configurable product coupons can be applied in cart
            Mage::getSingleton('core/session', array('name' => 'frontend'));            
            $session = Mage::getSingleton('checkout/session');
            $coupons = $session->getMappCoupon();                    
            $coupons = explode(",",$coupons);
            $coupons[] = $coupon;
            $coupons = implode(",",$coupons);
            $session->setMappCoupon($coupons);
        } else if(!empty($cartParams['productID'])){
            //Load and add product to cart
            $product = Mage::getModel('catalog/product')->load($cartParams['productID']);
            Mage::getModel('mapp/mapp')->addProductToCart($product);
            //20140129 AP DISABLED - This method will not update price because module now requires coupon codes in session
            //Mage::getModel('mapp/mapp')->updateCartByMappProductID($product);
        }
        
        //Redirect to product page if configurable item is not already in the cart
        if(!empty($product) && $product->isConfigurable()){
            //Only redirect if item is not already in cart
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            if (!$quote->hasProductId($product->getData('entity_id'))) {
                Mage::getSingleton('core/session')->addError('Please select an option below to add item to cart.');
                Mage::app()->getResponse()->setRedirect($product->getProductUrl());
            }
        } else {
            //Redirect to Cart
            $carturl = Mage::helper('checkout/url')->getCartUrl();
            Mage::app()->getResponse()->setRedirect($carturl);
        }
    }
}