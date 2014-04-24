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

class Primeinteractive_Mapp_Model_Mapp extends Mage_Core_Model_Abstract {
    
    public function _construct() {
        parent::_construct();
        $this->_init('mapp/mapp');
    }

    public function modifyCart() {
        //Get coupons from session, create array, remove any duplicate or empty values
        Mage::getSingleton('core/session', array('name' => 'frontend'));            
        $session = Mage::getSingleton('checkout/session');
        $coupons = $session->getMappCoupon();
        $coupons = explode(",",$coupons);
        $coupons = array_unique($coupons);
        $coupons = array_filter($coupons, 'strlen');
        
        //Remove invalid coupons before applying valid coupons
        $valid = $this->_purgeCoupons($coupons);
        
        //Apply valid coupons
        $this->_applyCoupons($valid);
        
        //Set valid coupons into session
        $valid = implode(",",$valid);
        $session->setMappCoupon($valid);
    }
    
    protected function _purgeCoupons($coupons){
        $validProductIds = array();
        $validCoupons = array();
        $invalidCoupons = array();

        //Identify valid and invalid coupons
        foreach($coupons as $coupon){
            $expired = $this->_isCouponExpired($coupon);
            if ($expired != true) {
                $mapp = $this->getCollection()->addFieldToFilter('coupon_code', $coupon)->getFirstItem();
                $validProductIds[] = Mage::getModel("catalog/product")->getIdBySku($mapp->getData('sku'));
                $validCoupons[] = $coupon;
            }
            else {
                $invalidCoupons[] = $coupon;
            }
        }

//        //Reset pricing for all items - This method catches deleted coupons but might more resource intensive???
//        $this->_resetAllCartPrices();

        //Reset pricing only for invalid coupons - Will not reset pricing for deleted coupons
        foreach($invalidCoupons as $coupon){
            $mapp = Mage::getModel('mapp/mapp')->getCollection()->addFieldToFilter('coupon_code', $coupon)->getFirstItem();
            $invalidProductId = Mage::getModel("catalog/product")->getIdBySku($mapp->getData('sku'));
            
            if(!in_array($invalidProductId, $validProductIds)){
                //Set regular pricing
                $this->_updateCart($invalidProductId);
                Mage::getSingleton('core/session')->addError('Coupon code "' . $coupon . '" is no longer valid.');
            }
        }
        
        return $validCoupons;
    }
    
    protected function _applyCoupons($valid){
        foreach($valid as $coupon_code){
//            Mage::log($coupon_code, null, 'mapp.txt');
            
            //Apply Mapp Pricing
            $mapp = $this->getCollection()->addFieldToFilter('coupon_code', $coupon_code)->getFirstItem();
            $productID = Mage::getModel("catalog/product")->getIdBySku($mapp->getData('sku'));
            if($productID != 0){
                //Load product object
                $product = Mage::getModel('catalog/product')
                                ->setStoreId(Mage::app()->getStore()->getId())
                                ->load($productID);

                //get Mapp price attribute value
                $attributeName = 'map_price';
                $attributes = $product->getAttributes();
                $map_price = 0;
                if(array_key_exists($attributeName , $attributes)){
                    $attributesobj = $attributes["{$attributeName}"];
                    $map_price = $attributesobj->getFrontend()->getValue($product);
                }

                if ($map_price > 0) {
                    try {
                        //Apply mapp price and set message
                        $this->_updateCart($productID, $map_price);
                    } catch (Exception $ex) {
                        Mage::getSingleton('core/session')->addError('Unable to apply MAP Pricing for Coupon ' . $coupon_code);
                        //echo $ex->getMessage();
                        //Mage::log($ex);
                    }
                }
            } else {
                Mage::log('unable to identify product associated with coupon code: ' . $coupon_code);
            }
        }
    }

    protected function _updateCart($productID, $price = null) {
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        //Then, to get the list of items in the cart :
        $cartItems = $quote->getAllVisibleItems();

        //Then, to get the count for each item :
        foreach ($cartItems as $item) {
            // Only update the price of this product
            if($item->getProduct()->getId()==$productID){
                //Set regular price if price is empty
                if($price == null){
                    $product= Mage::getModel('catalog/product')->load($productID);
                    $price = $product->getPrice();
                }
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                //Set flag for template formatting
                $item->setIsMappPrice(true);
                $item->getProduct()->setIsSuperMode(true);
            }
        }

        // very straightforward, set the cart as updated
        // Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
    }
    
    protected function _isCouponExpired($couponCode){
        $mapp_request = $this->getCollection()->addFieldToFilter('coupon_code',$couponCode)->getFirstItem();
        $created_timestamp = $mapp_request->getData('created_time');
        if($created_timestamp == ''){
            return true;
        }
        
        $expiration = Mage::getStoreConfig('mapp_code/mapp_group/mapp_expiration');
        $expiration_date = new DateTime($created_timestamp);
        //Mage::log('Created Time: ' . $expiration_date ->format('F d, Y h:i:s A T'), null, 'mapp.txt');
        $expiration_date->add(new DateInterval($expiration));
        //Mage::log('Expiration Time: ' . $expiration_date ->format('F d, Y h:i:s A T'), null, 'mapp.txt');

        $now = new DateTime();
        //Mage::log('Current Time: ' . $now->format('F d, Y h:i:s A T'), null, 'mapp.txt');
        if($now > $expiration_date){
            //Coupon Expired
            return true;
        } else {
            //Coupon Valid
            return false;
        }
    }
    
    public function sendMappEmail($id){
        $mapp = Mage::getModel('mapp/mapp')->load($id);
        $product_id = Mage::getModel("catalog/product")->getIdBySku( $mapp->getData('sku') );

        if($product_id) {
            //Load product object
            $product = Mage::getModel('catalog/product')->load($product_id);
            
            //Get Price Values
            $RegPrice = $product->getPrice();
            $MappPrice = $product->getMapPrice();
            $Discount = $RegPrice - $MappPrice;

            //Get Store Id
            $storeId = $mapp->getData('store_id');
            
            //Format Created Time from Mapp model
            $date = new DateTime($mapp->getData('created_time'));
            $date->setTimezone(new DateTimeZone('America/Chicago'));
            $created_time_formatted = $date->format('F d, Y h:i:s A T');
  
            //Set variables for use in Email template
            $vars = array(
                'sku'               => $mapp->getData('sku'),
                'created_time'      => $created_time_formatted,
                'manufacturer'      => $product->getAttributeText('manufacturer'),
                'manufacturer_sku'  => $product->getData('manufacturer_sku'),
                'product_url'       => $mapp->getData('producturl'),
                'product_name'      => $mapp->getData('productname'),
                'product_image_url' => $product->getImageUrl(),
                'sender_name'       => Mage::getStoreConfig('trans_email/ident_general/name', $storeId),
                'coupon_code'       => $mapp->getData('coupon_code'),
                'mapp_price'        => Mage::helper('core')->currency($MappPrice, true, false),
                'discount'          => Mage::helper('core')->currency($Discount, true, false),
                'reg_price'         => Mage::helper('core')->currency($RegPrice , true, false),
                'expiration'        => Mage::helper('mapp')->getCurrentExpirationTimeframe(),
                'cart_url'          => Mage::helper('mapp')->buildCartUrl($mapp->getData('coupon_code'), $storeId),
                'store_logo_url'    => Mage::helper('mapp')->buildStoreLogoUrl($storeId),
                'store_logo_alt'    => Mage::getStoreConfig('design/header/logo_alt', $storeId),
                'store_email'       => Mage::getStoreConfig('trans_email/ident_support/email', $storeId),
                'store_phone'       => Mage::getStoreConfig('general/store_information/phone', $storeId),
            );

            //Set Email Template
            $templateId = Mage::getStoreConfig('mapp_code/mapp_group/mapp_email_template');
            $translate = Mage::getSingleton('core/translate');
            
            //Set Admin Email
            $adminArray = array(
                'name' => Mage::getStoreConfig('trans_email/ident_general/name', $storeId),
                'email' => Mage::getStoreConfig('trans_email/ident_general/email', $storeId),
            );
            
            //Set Customer Email
            $customerArray = array(
                'name' => $mapp->getData('name'),
                'email' => $mapp->getData('emailid'),
            );
            
            // Send Transactional Email to Customer
            Mage::getModel('core/email_template')->addBcc($adminArray['email'])->sendTransactional($templateId, $adminArray, $customerArray['email'], $customerArray['name'], $vars, $storeId);
            $translate->setTranslateInline(true);

            // Send Transactional Email copy to Admin
            //Mage::getModel('core/email_template')->sendTransactional($templateId, $customerArray, $adminArray['email'], $adminArray['name'], $vars, $storeId);
            //$translate->setTranslateInline(true);
        } else {
            Mage::log('Unable to find product with sku: '.$mapp->getData('sku').'. Mapp email not sent to customer ' . $customerArray['email'], null, 'mapp_errors.txt');
        }
    }
        
    public function isCouponExpiredByMappID($id) {
        $mapp = $this->load($id);
        return $this->_isCouponExpired($mapp->getData('coupon_code'));
    }
    
    public function addProductToCart($product){
        //Only add simple products to cart automatically
        if(!$product->isConfigurable()){
            //Add to cart if not already in cart
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            if (!$quote->hasProductId($product->getData('entity_id'))) {
                $cart = Mage::getSingleton('checkout/cart');
                $cart->addProduct($product, array('qty' => '1'));
                $cart->save();
            }
        }
    }

    /*
     * 20140129 AP DISABLED - This Method will not update price because module 
     * has been modified to use coupon codes in session to set prices.
     */
    public function updateCartByMappProductID($product){
        $attributeName = 'map_price';
        $attributes = $product->getAttributes();
        $map_price = null;
        if(array_key_exists($attributeName , $attributes)){
            $attributesobj = $attributes["{$attributeName}"];
            $map_price = $attributesobj->getFrontend()->getValue($product);
            //Mage::log($product->getData('entity_id'), null, 'mapp.txt');
            //Mage::log($map_price, null, 'mapp.txt');
            $this->_updateCart($product->getData('entity_id'), $map_price);
        }
    }
    
//    protected function _resetAllCartPrices() {
//        $quote = Mage::getSingleton('checkout/session')->getQuote();
//
//        //Then, to get the list of items in the cart :
//        $cartItems = $quote->getAllVisibleItems();
//
//        //Then, to get the count for each item :
//        foreach ($cartItems as $item) {
//            $product= Mage::getModel('catalog/product')->load($item->getProduct()->getId());
//            $price = $product->getPrice();
//            $item->setCustomPrice($price);
//            $item->setOriginalCustomPrice($price);
//            $item->getProduct()->setIsSuperMode(true);
//        }
//    }
}