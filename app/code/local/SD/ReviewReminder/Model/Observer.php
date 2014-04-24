<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Filters
 *
 * @author dustinmiller
 */
class SD_ReviewReminder_Model_Observer {
    
    public function reminderMaintenance() {
        if(!Mage::getStoreConfig('reviewreminder/settings/reviewreminder_enabled')) {
            return;
        }
        //need to make this a configuration value
        $startdate = date('Y-m-d H:i:s',time() - (60 * 24 * 60 * 60));
        $firstTime = Mage::getStoreConfig('reviewreminder/reviewreminder_first/elapsed_time');
        $defaultTime = Mage::getStoreConfig('reviewreminder/reviewreminder_first/default_time');
        $firstEmail = date('Y-m-d H:i:s',time() - ($firstTime * 24 * 60 * 60));
        $defaultEmail = date('Y-m-d H:i:s',time() - ($defaultTime * 24 * 60 * 60));
        
        $reminderCollection = Mage::getModel('reviewreminder/reviewReminder')->getCollection()
                ->addFieldToSelect('id')
                ->addFieldToSelect('order_id');

        $reminderArray = array(0 => 0);
        
        foreach ($reminderCollection as $orderId) {
            array_push($reminderArray, $orderId->getOrderId());
        }
        
        $ordersWithoutReminderObject = Mage::getModel("sales/order")->getCollection()
                ->addFieldToSelect('entity_id')
                ->addFieldToSelect('customer_firstname')
                ->addFieldToSelect('customer_lastname')
                ->addFieldToFilter('entity_id', array('nin' => $reminderArray))
                ->addFieldToFilter('customer_email', array('neq' => ''))
                //->addFieldToFilter('customer_email', array('eq' => 'dustinmiller@servu-online.com'))
                ->addFieldToFilter('customer_id', array('neq' => ''))
                ->addFieldToSelect('customer_email')
                ->addFieldToSelect('customer_id')
                ->addFieldToSelect('created_at')
                ->addFieldToSelect('store_id')
                ->addFieldToFilter('created_at', array('gt' => $startdate));
        
        foreach($ordersWithoutReminderObject as $item){
            $reviewModel = Mage::getModel('reviewreminder/reviewReminder');
            
            $uniqueId = uniqid($item->getEntityId());
            
            $reviewModel->setData('tracking_url',$uniqueId);
            $reviewModel->setData('customer_name',$item->getCustomerFirstname().' '.$item->getCustomerLastname());
            $reviewModel->setData('order_id',$item->getEntityId());
            $reviewModel->setData('store_id',$item->getStoreId());
            $reviewModel->setData('is_active', 1);
            $reviewModel->setData('created_at',date('Y-m-d H:i:s',time()));
            $reviewModel->setData('updated_at',date('Y-m-d H:i:s',time()));
            $reviewModel->setData('ordered_date',$item->getCreatedAt());
            $reviewModel->setData('customer_email',$item->getCustomerEmail());
            $reviewModel->setData('email_status',0);
            
            try {
                $reviewModel->save();
            } 
            catch (Exception $e){
                Mage::logException($e);
            }
            
        }
        $reminderCollection = null;
        $reminderCollection = Mage::getModel('reviewreminder/reviewReminder')->getCollection()
                ->addFieldToFilter('email_status', array('lt' => 2))
                ->addFieldToFilter('is_active', array('eq' => 1));
        //change 199
        foreach($reminderCollection as $mailed){
            $orderModel = Mage::getModel('sales/order')->load($mailed->getOrderId());
            $emailNumber = 0;
            $emailWord = "";
            $storeId = $orderModel->getStoreId();

            if($orderModel->getId() == null) {
                break;
            }
            
            $orderItems = $orderModel->getItemsCollection();
            $currentTime = date('Y-m-d H:i:s',time());
            
            switch ($mailed->getEmailStatus()) {
                case 0:
                    $emailWord = "first";
                    $emailNumber = 1;
                    break;
                case 1:
                    $emailWord = "second";
                    $emailNumber = 2;
                    break;
            }
            
            if($mailed->getEmailStatus() == 0) {
                $date = $orderModel->getCreatedAt();
            } elseif($mailed->getEmailStatus() == 1) {
                $lastMailed = Mage::getModel('reviewreminder/emails')->loadByOrderIdEmailNum($orderModel->getId(),$mailed->getEmailStatus());
                $date = $lastMailed->getMailedAt();
            } else {
                continue;
            }
            
            if (($emailWord == "first") && ($orderModel->getStatus() != 'complete')) {
                $timestamp = strtotime ('+'.Mage::getStoreConfig('reviewreminder/reviewreminder_'.$emailWord.'/default_time').' day', strtotime ($date));
            } else {
                $timestamp = strtotime ('+'.Mage::getStoreConfig('reviewreminder/reviewreminder_'.$emailWord.'/elapsed_time').' day', strtotime ($date));
            }
            $timestamp = date('Y-m-d H:i:s', $timestamp);
            
            $productIds = array();
            $products = array();
            
            if($timestamp <= $currentTime) {
                foreach($orderItems as $oItem) {
                    if(!$oItem->getParentItemId()){
                        $productIds[] = $oItem->getProductId();
                        $product = Mage::getModel('catalog/product')->load($oItem->getProductId());
                        $product['url'] = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$product->getUrlPath();//$product->getProductUrl(true);
                        $product['image'] = (string)Mage::helper('catalog/image')->init($product, 'image')->resize(75);
                        $products[] = $product;
                    }
                }
            
                $sent = Mage::getModel("reviewreminder/emails");
                $sent->setData('review_mailed_id',$mailed->getId());
                $sent->setData('order_id',$mailed->getOrderId());
                $sent->setData('mailed_at',date('Y-m-d H:i:s',time()));
                $sent->setData('email_number',$emailNumber);
                
                try {
                    $sent->save();
                } 
                catch (Exception $e){
                    Mage::logException($e);
                }
                
                //if($mailed->getCustomerEmail() == "dustinmiller@servu-online.com") {
                    if($orderModel->getId() != '') {
                        $this->sendReviewReminder($orderModel,$products,$emailWord,$mailed->getTrackingUrl());
                    }
                //}
                
                $mailed->setData('email_status', $emailNumber);
                $mailed->setData('updated_at', date('Y-m-d H:i:s',time()));

                try {
                    $mailed->save();
                } 
                catch (Exception $e){
                    Mage::logException($e);
                }
            }
        }
        
        $promotionId = Mage::getStoreConfig('reviewreminder/settings/reviewreminder_promotion');
        
        if($promotionId == null) {
            return;
        }
        
        $reminderCollection = null;
        $reminderCollection = Mage::getModel('reviewreminder/reviewReminder')->getCollection()
                ->addFieldToFilter('email_status', array('eq' => 200));
        $rule = Mage::getModel('salesrule/rule')->load($promotionId); 
        
        $generator = Mage::getModel('salesrule/coupon_massgenerator');    
        $generator->setFormat(Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC);     
        $generator->setDash(4);
        $generator->setLength(12);
        $rule->setCouponCodeGenerator($generator);
        $rule->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO);
        
        foreach($reminderCollection as $promotion) {
            $coupon = $rule->acquireCoupon();
            $promotionCode = $coupon->getCode();
            $promotion->setData('coupon_code',$promotionCode);
            $promotion->setData('email_status',201);
            $promotion->setData('updated_at', date('Y-m-d H:i:s',time()));
            
            $sent = Mage::getModel("reviewreminder/emails");
            $sent->setData('review_mailed_id',$promotion->getId());
            if($promotion->getOrderId() != '') {
                $sent->setData('order_id',$promotion->getOrderId());
            }

            $sent->setData('mailed_at',date('Y-m-d H:i:s',time()));
            $sent->setData('email_number',201);
            
            //if($promotion->getCustomerEmail() == "dustinmiller@servu-online.com") {  
                $this->sendCoupon($promotion->getCustomerName(),$promotion->getCustomerEmail(), $promotionCode, $promotion->getStoreId());
            //}
            
            try {
                $coupon->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)->save(); 
                $promotion->save();
                $sent->save();
            } 
            catch (Exception $e){
                Mage::logException($e);
            }
        }
        
        $reminderCollection = null;
        $reminderCollection = Mage::getModel('reviewreminder/reviewReminder')->getCollection()
                ->addFieldToFilter('email_status', array('eq' => 201));
        $reminderCollection->couponsUsed();
        
        foreach($reminderCollection as $couponUsed) {
            $couponUsed->setData('email_status',205);
            $couponUsed->setData('coupon_order_id',$couponUsed->getIncrementId());

            try {
                $couponUsed->save();
            } 
            catch (Exception $e){
                Mage::logException($e);
            }
        }
        
    }
    
    private function sendReviewReminder($order,$products,$emailType,$trackingCode) {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
 
        $storeId = $order->getStoreId();

        Mage::getModel('core/email_template')
        ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
        ->sendTransactional(
            Mage::getStoreConfig('reviewreminder/reviewreminder_'.$emailType.'/email_template', $storeId),
            Mage::getStoreConfig('reviewreminder/reviewreminder_'.$emailType.'/sender_identity', $storeId),
            $order->getCustomerEmail(),
            $order->getCustomerFirstName().' '.$order->getCustomerMiddleName().' '.$order->getCustomerLastName(),
            array('products'=>$products, 'order' => $order, 'trackingCode' => $trackingCode)
        );
 
        $translate->setTranslateInline(true);
    }
    
    private function sendCoupon($customerName, $customerEmail, $promotioncode, $storeId) {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        Mage::getModel('core/email_template')
        ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
        ->sendTransactional(
            Mage::getStoreConfig('reviewreminder/settings/promotion_email_template', $storeId),
            Mage::getStoreConfig('reviewreminder/settings/promotion_sender_identity', $storeId),
            $customerEmail,
            $customerName,
            array('name'=>$customerName, 'promotioncode' => $promotioncode)
        );
 
        $translate->setTranslateInline(true);
    }
    
    public function reviewCheck($observer) { 
        $event = $observer->getEvent()->getObject();

        if ($event->getConfirmemail() != '' || $event->getRatings() == '') { 
            Mage::throwException(Mage::helper('reviewreminder')->__('Spam'));
            return;
        }
        
        if(!Mage::getStoreConfig('reviewreminder/settings/reviewreminder_enabled')) {
            return;
        }

        $itemFound = false;
        
        if($responsecode = Mage::getSingleton('core/session')->getReviewTrackCode()) {

            $reviewModel = Mage::getModel('reviewreminder/reviewReminder')->loadByTrackingCode($responsecode);
            $order = Mage::getModel("sales/order")->load($reviewModel->getOrderId());
            
            $orderItems = $order->getItemsCollection();
            foreach ($orderItems as $oi) {        
                if($oi->getProductId() == $event->getEntityPkValue()) {
                    if($reviewModel->getId() != null && $reviewModel->getEmailStatus() < 200) {
                        $itemFound = true;
                        $reviewModel->setData('updated_at',date('Y-m-d H:i:s',time()));
                        $reviewModel->setData('email_status',200);
                        Mage::getSingleton('core/session')->unsReviewTrackCode();

                        try {
                            $reviewModel->save();
                            return;
                        } 
                        catch (Exception $e){
                            Mage::logException($e);
                        }
                    }
                    if($itemFound) {
                        break;
                    }
                }
            }
        }
        
        if($event->getCustomerId() != '' && !$itemFound) {
            $orderCollection = Mage::getModel("sales/order")->getCollection()
                ->addFieldToSelect('entity_id')
                ->addFieldToFilter('customer_id', array('eq' => $event->getCustomerId()));
            foreach($orderCollection as $item) {
                if($itemFound) {
                    break;
                }
                $orderItems = $item->getItemsCollection();
                foreach ($orderItems as $oi) {        
                    if($oi->getProductId() == $event->getEntityPkValue()) {
                        $reviewModel = Mage::getModel('reviewreminder/reviewReminder')->loadByOrderId($item->getId());
                        if($reviewModel->getId() != null && $reviewModel->getEmailStatus() < 200) {
                            $itemFound = true;
                            $reviewModel->setData('updated_at',date('Y-m-d H:i:s',time()));
                            $reviewModel->setData('email_status',200);

                            try {
                                $reviewModel->save();
                                return;
                            } 
                            catch (Exception $e){
                                Mage::logException($e);
                            }
                        }
                        if($itemFound) {
                            break;
                        }
                    }
                }
            }  
        }	
        
        $customerEmail = '';

        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerEmail = Mage::getModel('customer/customer')->load($customer->getId())->getData('email');
        }
        else {
            $customerEmail = $event->getEmail();
        }
        
        $reviewModel = Mage::getModel('reviewreminder/reviewReminder');
        $reviewModel->setData('customer_name',$event->getNickname());
        $reviewModel->setData('store_id',$event->getStoreId());
        $reviewModel->setData('is_active', 1);
        $reviewModel->setData('created_at',date('Y-m-d H:i:s',time()));
        $reviewModel->setData('updated_at',date('Y-m-d H:i:s',time()));
        $reviewModel->setData('customer_email', $customerEmail);
        $reviewModel->setData('email_status',200);

        try {
            $reviewModel->save();
        } 
        catch (Exception $e){
            Mage::logException($e);
        } 
    }
    
    public function trackResponse($observer) {
        $responseCode = Mage::app()->getRequest()->getParam('bGytUhk');
        
        if($responseCode != '') {
            Mage::getSingleton('core/session')->setReviewTrackCode($responseCode);        

            $reviewModel = Mage::getModel('reviewreminder/reviewReminder')->loadByTrackingCode($responseCode);
            
            if($reviewModel->getId() != null ) {
                $reviewModel->setData('recovered', 1);
                $reviewModel->setData('recovered_on', date('Y-m-d H:i:s',time()));

                try {
                    $reviewModel->save();
                } 
                catch (Exception $e){
                    Mage::logException($e);
                }
            }
        }  
    }
    
}

?>
