<?php
class SD_Acm_Model_Observer {
    
    public function acmMaintenance() {
        //first we need to grab all the relevant data from the quote table and our table
        //next, we need to figure out what emails neeed to be sent based on the three time stamps
        if(!Mage::getStoreConfig('acm/settings/acm_enabled')) {
            return;
        }
        $time = date('Y-m-d H:i:s',time() - (5 * 24 * 60 * 60));

        $quoteCollection = Mage::getModel("sales/quote")->getCollection()
                ->addFieldToSelect(array('entity_id'))
                ->addFieldToFilter('is_active', array('eq' => 1))
                ->addFieldToFilter('customer_email', array('neq' => ''))
                ->addFieldToSelect('subtotal')
                ->addFieldToSelect('created_at')
                ->addFieldToSelect('updated_at')
                ->addFieldToSelect('store_id')
                ->addFieldToSelect('customer_id')
                ->addFieldToFilter('updated_at', array('gt' => $time))
                ->addFieldToFilter('base_subtotal', array('gt' => 0.00));
        foreach($quoteCollection as $item) {
            $customerModel = Mage::getModel('sd_acm/unsubscribe')->loadCustomerId($item->getCustomerId(),$item->getStoreId());
            //add a quote creation date and a cart update date to keep track of when mail was sent
            $cartModel = null;
            $cartModel = Mage::getModel('sd_acm/acm')->loadByQuoteId($item->getEntityId());
            $abandonedTime = date('Y-m-d H:i:s',strtotime('+25 minute', strtotime ($item->getUpdatedAt())));
    
            if($cartModel->getId() == '' && ($customerModel->getIsActive() || $customerModel->getId() == '') && $abandonedTime < date('Y-m-d H:i:s',time())) {
                $cartModel->setData('abandoned_date',$item->getUpdatedAt());
                $cartModel->setData('quote_id', $item->getEntityId());
                $cartModel->setData('store_id', $item->getStoreId());
                $cartModel->setData('created_at', date('Y-m-d H:i:s',time()));
                $cartModel->setData('updated_at', $item->getCreatedAt());
                $cartModel->setData('is_active', 1);
                $cartModel->setData('initial_cart_amount', $item->getSubtotal());
                $cartModel->setData('ordered', 0);
                $cartModel->setData('has_recovered', 0);
                $cartModel->setData('status', 0); 
                try {
                    $cartModel->save();
                } 
                catch (Exception $e){
                    Mage::logException($e);
                }
            }
            //load all abandoned carts based on active state 
            //then loop through collection and see if an order exists with quote ide
            //if not, send out an email based on current status (should be state) then
            //update model     
        }
        
        $mailedCartCollection = Mage::getModel("sd_acm/acm")->getCollection()
                ->addFieldToFilter('is_active', array('eq' => 1))
                ->addFieldToFilter('yesnoContacted', array('eq' => 0));
        foreach($mailedCartCollection as $mailed){
            $orderModel = Mage::getModel('sales/order')->loadByAttribute('quote_id', $mailed->getQuoteId());
            $sendEmail = false;
            $deactivate = false;
            $emailNumber = 0;
            $emailWord = "";
           
            $quote = Mage::getModel("sales/quote")->loadByIdWithoutStore($mailed->getQuoteId());
            
            if($orderModel->getId() == null) {

                //This is where the magic happens!
                
                $quoteItems = Mage::getModel("sales/quote_item")->getCollection()->setQuote($quote);
                $customer = Mage::getModel('sd_acm/unsubscribe')->loadCustomerId($quote->getCustomerId(),$quote->getStoreId());
                
                if(!$customer->getIsActive() && $customer->getId() != '') {
                    $deactivate = true;
                    $mailed->setData('is_active', 0);
                }
                
                $productIds = array();
                $products = array();
                
                $currentTime = date('Y-m-d H:i:s',time());
                
                //Determine which email should go out.
                if(!$deactivate) {
                    switch ($mailed->getStatus()) {
                        case 0:
                            $emailWord = "first";
                            $emailNumber = 1;
                            break;
                        case 1:
                            $emailWord = "second";
                            $emailNumber = 2;

                            if(!Mage::getStoreConfig('acm/acm_second/email_enabled')) {
                                $deactivate = true;
                            }

                            break;
                        case 2:
                            $emailWord = "third";
                            $emailNumber = 3;
                            $amount = false;
                            $items = false;

                            if(Mage::getStoreConfig('acm/acm_third/cart_amount') > 0 && $quote->getSubtotal() >= Mage::getStoreConfig('acm/acm_third/cart_amount')) {
                                $amount = $quote->getSubtotal();
                            }
                            elseif(Mage::getStoreConfig('acm/acm_third/cart_amount') == 0 || Mage::getStoreConfig('acm/acm_third/cart_amount') == '') {
                                $amount = true;
                            }

                            if(Mage::getStoreConfig('acm/acm_third/cart_items') > 0 && $quote->getItemsQty() >= Mage::getStoreConfig('acm/acm_third/cart_items')) {
                                $items = $quote->getItemsQty();
                            }
                            elseif(Mage::getStoreConfig('acm/acm_third/cart_items') == 0 || Mage::getStoreConfig('acm/acm_third/cart_items') == '') {
                                $items = true;
                            }

                            if(!Mage::getStoreConfig('acm/acm_third/email_enabled') || !$amount || !$items) {
                                $deactivate = true;
                            }
                            break;
                        }
                }
                
            }
            else {
                $mailed->setData('ordered', 1);
                $mailed->setData('is_active', 0);
                $deactivate = true;
            }
            
            if($mailed->getStatus() == 0) {
                $date = $quote->getUpdatedAt();
            } else {
                $lastMailed = Mage::getModel('sd_acm/emails')->loadByQuoteIdEmailNum($quote->getEntityId(),$mailed->getStatus());
                $date = $lastMailed->getMailedAt();
            }
            
            $timestamp = strtotime ('+'.Mage::getStoreConfig('acm/acm_'.$emailWord.'/elapsed_time').' minute', strtotime ($date)) ;
            $timestamp = date ('Y-m-d H:i:s', $timestamp);
            
            if($timestamp <= $currentTime & !$deactivate) {
                $sendEmail = true;
            }
            
            if($sendEmail) {
                foreach($quoteItems as $qItem) {
                    if(!$qItem->getParentItemId()){
                        $productIds[] = $qItem->getProductId(); 
                        $product = Mage::getModel('catalog/product')->load($qItem->getProductId());
                        $product['carturl'] = $product->getProductUrl(true);
                        $product['cartimage'] = (string)Mage::helper('catalog/image')->init($product, 'image')->resize(75);
                        $product['cartqty'] = $qItem->getQty();
                        $product['cartsub'] = Mage::helper('core')->formatPrice(($qItem->getQty() * $qItem->getPrice()), false);
                        $products[] = $product;
                    }
                }
                $sent = Mage::getModel("sd_acm/emails");
                $sent->setData('cart_mailed_id',$mailed->getId());
                $sent->setData('quote_id',$mailed->getQuoteId());
                $sent->setData('mailed_at',date('Y-m-d H:i:s',time()));
                $sent->setData('email_number',$emailNumber);
                try {
                    $sent->save();
                } 
                catch (Exception $e){
                    Mage::logException($e);
                }
                $this->sendEmail($quote,$products,$emailWord);
                $mailed->setData('status', $emailNumber);
                if($emailNumber == 3) {
                    $deactivate = true;
                }
                $mailed->setData('updated_at', date('Y-m-d H:i:s',time()));
            } 
            
            if($deactivate) {
                $mailed->setData('updated_at', date('Y-m-d H:i:s',time()));
            } elseif ($mailed->getAbandonedDate() != $quote->getUpdatedAt() && !$mailed->getHasRecovered()) {
                $mailed->setData('has_recovered', 1);
                $mailed->setData('recovered_date', $quote->getUpdatedAt());
                $mailed->setData('updated_at', date('Y-m-d H:i:s',time()));
                if(!Mage::getStoreConfig('acm/settings/recovered_enabled')){
                    $mailed->setData('is_active', 0);
                }
            } 
            
            try {
                $mailed->save();
            } 
            catch (Exception $e){
                Mage::logException($e);
            }
        }
    }
    
    private function sendEmail($quote,$products,$emailType) {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
 
        $storeId = $quote->getStoreId();

        Mage::getModel('core/email_template')
        ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
        ->sendTransactional(
            Mage::getStoreConfig('acm/acm_'.$emailType.'/email_template', $storeId),
            Mage::getStoreConfig('acm/acm_'.$emailType.'/sender_identity', $storeId),
            $quote->getCustomerEmail(),
            $quote->getCustomerFirstName().' '.$quote->getCustomerMiddleName().' '.$quote->getCustomerLastName(),
            array('products'=>$products, 'quote' => $quote)
        );
 
        $translate->setTranslateInline(true);
    }
    
    public function unsubscribeCartNotification(Varien_Event_Observer $observer) {       
        //Event observer for unsubscriptions
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getEntityId();
        $storeId = Mage::app()->getStore()->getStoreId();
        $isActive = 0;

        if(Mage::app()->getFrontController()->getRequest()->getParam('is_cart_notify')  == 1) {
            $isActive = 1;
            Mage::getSingleton('customer/session')->addSuccess('Cart Notifications are ON');
        } else {
            Mage::getSingleton('customer/session')->addSuccess('Cart Notifications are OFF');
        }
        $customer = Mage::getModel('sd_acm/unsubscribe')->loadCustomerId($customerId, $storeId);

        if($customer->getId() == '') {
                $customer->setData('store_id',$storeId);
                $customer->setData('customer_id',$customerId);
        }
        $customer->setData('is_active',$isActive);
        $customer->save();    
    }
}
?>
