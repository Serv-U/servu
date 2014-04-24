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

class Primeinteractive_Mapp_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function buildCartURL($couponCode, $storeId){
        $base_url = Mage::app()->getStore($storeId)->getBaseUrl();
        return $base_url . "/mapp/index/redirectToCart?couponCode=$couponCode";
    }
    
    public function buildStoreLogoUrl($storeId){
        $base = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
        
        $package = Mage::getStoreConfig('design/package/name', $storeId);
        if($package == ''){ $package = 'default'; }
        
        $skin_name = Mage::getStoreConfig('design/theme/skin', $storeId);
        if($skin_name == ''){ $skin_name = 'default'; }
        
        $img = Mage::getStoreConfig('design/header/logo_src', $storeId);
        return $base . 'frontend/' . $package . '/' . $skin_name . '/' . $img;
    }
    
    public function generateCouponCode($i = null){
        //Generate random coupon code
        $max_iterations = 100;
        $coupon_code = uniqid('mapp');
        
        //Verify that random coupon code does not exist in db
        $match = Mage::getModel('mapp/mapp')
                        ->getCollection()
                        ->addFieldToFilter('coupon_code', $coupon_code)
                        ->getFirstItem();
        $match_code = $match->getData('coupon_code');
        
        if(!empty($match_code) && $i < $max_iterations){
            $i++;
            //Restrict number of iterations and create error log if still unable to generate unique a coupon code
            if($i == $max_iterations) {
                Mage::log('Duplicate coupon code issued in MAPP Module. Coupon_code: '.$coupon_code, null, 'mapp_error.txt');
                return $coupon_code;
            }
            //Recusively call function if coupon code already exists
            return $this->generateCouponCode($i);
        }
        
        return $coupon_code;
    }
    
    public function getExpirationTimeframes(){
        return array(
            'P1D' => '24 Hours',
            'P1W' => 'One Week',
            'P1M' => 'One Month',
            'P3M' => 'Three Months',
            'P6M' => 'Six Months',
            'P1Y' => 'One Year',
            //'P0D' => 'Disable All Coupon Codes',
        );
    }
    
    public function getCurrentExpirationTimeframe(){
        $expiration_code = Mage::getStoreConfig('mapp_code/mapp_group/mapp_expiration');
        $expiration_array = Mage::helper('mapp')->getExpirationTimeframes();
        return $expiration_array["$expiration_code"];
    }
    
    public function getCustomerName($email = null){
        $fullname = "Guest";
        
        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            //Get Name from logged in user
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $firstname = $customer->getData('firstname');
            $lastname = $customer->getData('lastname');
        } else {
            //Get Name by search email in database (there can be duplicate emails across sites, right?)?
            $collection = Mage::getModel("customer/customer")->getCollection()->addAttributeToFilter("email",$email)->getFirstItem();
            $customer = Mage::getModel('customer/customer')->load($collection->getData('entity_id'));
            $firstname = $customer->getData('firstname');
            $lastname = $customer->getData('lastname');
        }

        //Set Name if not empty
        if(!empty($firstname) && !empty($lastname)){
            $fullname = $firstname . ' ' . $lastname;
        }
        
        return $fullname;
    }
}