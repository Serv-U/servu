<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Observer
 *
 * @author dustinmiller
 */
class ServU_Shipping_Model_Observer {

    public function saveCartBefore($evt) {
        Mage::getSingleton('core/session')->unsAccessorials();
    }
    
    public function saveQuoteBefore($evt) {
        $quote = $evt->getQuote();
            
        if(Mage::getSingleton('core/session')->getAccessorials()) {
            $accessorials = Mage::getSingleton('core/session')->getAccessorials();
            $quote->setAccessorials($accessorials);
        }
        
        if(Mage::registry('servu_shipping_model_shipping_confirmation')) {
            if(Mage::registry('servu_shipping_model_shipping_confirmation') != 'No Numbers') {
                $confirmationNumbers = Mage::registry('servu_shipping_model_shipping_confirmation');
                $quote->setConfirmationNumbers($confirmationNumbers);
            } 
            
            Mage::unregister('servu_shipping_model_shipping_confirmation');
            $model = Mage::getModel('servu_shipping/miscinformation_quote');
            $model->deleteByQuote($quote->getId(),'confirmation_number');
        } 
       
    }
    
    public function saveQuoteAfter($evt) {
        $quote = $evt->getQuote();
        $model = Mage::getModel('servu_shipping/miscinformation_quote');
        $model->deleteByQuote($quote->getId(),'s_extra_lfg');
        $model->deleteByQuote($quote->getId(),'s_extra_notify');
        $model->deleteByQuote($quote->getId(),'s_extra_residence');
        
        if($quote->getConfirmationNumbers()) {
            $numbers = $quote->getConfirmationNumbers();
            $model->deleteByQuote($quote->getId(),'confirmation_number');  
            
            for($i = 0; $i < count($numbers); $i++) {
                $model = Mage::getModel('servu_shipping/miscinformation_quote');
                $model->setQuoteId($quote->getId());
                $model->setKey('confirmation_number');
                $model->setValue($numbers[$i]);
                $model->save();
            }
        }

        if($quote->getAccessorials()) {
            
            $accessorials = $quote->getAccessorials();
           
            for($i = 0; $i < count($accessorials); $i++) {
                $model = Mage::getModel('servu_shipping/miscinformation_quote');
                $model->setQuoteId($quote->getId());
                $model->setKey($accessorials[$i]);
                $model->setValue($accessorials[$i]);
                $model->save();
            } 
        }
        $quote->setConfirmationNumbers(null);
        $quote->setAccessorials(null);
    }
    
    public function loadQuoteAfter($evt) {
        $quote = $evt->getQuote();
        $quoteNumbers = count($quote->getConfirmationNumbers());
        $accessorialsNumbers = count($quote->getAccessorials());
        $model = Mage::getModel('servu_shipping/miscinformation_quote');
        $data = $model->getByQuote($quote->getId());
        $confirmationNumbers = array();
        $accessorials = array();
        foreach($data as $key => $value){
            $key = preg_replace("([-0-9])", "", $key);
            if($key === 'confirmation_number' && $quoteNumbers > 0) {
                $confirmationNumbers[] = $value;
            }   
            elseif ($accessorialsNumbers > 0) {
                $accessorials[] = $value;
            }
        }
        $quote->setConfirmationNumbers($confirmationNumbers);
        $quote->setAccessorials($accessorials);
    }
    
     public function saveOrderAfter($evt) {
        $order = $evt->getOrder();
        $quote = $evt->getQuote();  
        $quoteModel = Mage::getModel('servu_shipping/miscinformation_quote');
        
        $data = $quoteModel->getByQuote($quote->getId());
        $confirmationNumbers = array();
        $accessorials = array();
        foreach($data as $key => $value){
            $key = preg_replace("([-0-9])", "", $key);
            if($key === 'confirmation_number') {
                $confirmationNumbers[] = $value;
            }   
            else {
                $accessorials[] = $value;
            }
        }
        
        if(count($confirmationNumbers) > 0){;
            $model = Mage::getModel('servu_shipping/miscinformation_order');
            $model->deleteByOrder($order->getId(),'confirmation_number');
            for($i = 0; $i < count($confirmationNumbers); $i++) {
                $model = Mage::getModel('servu_shipping/miscinformation_order');
                $model->setOrderId($order->getId());
                $model->setKey('confirmation_number');
                $model->setValue($confirmationNumbers[$i]);
                $model->save();
            }
            $order->setConfirmationNumbers($confirmationNumbers);
        }
        
        if(count($accessorials) > 0) {
            $model = Mage::getModel('servu_shipping/miscinformation_order');
            $model->deleteByOrder($quote->getId(),'s_extra_lfg');
            $model->deleteByOrder($quote->getId(),'s_extra_notify');
            $model->deleteByOrder($quote->getId(),'s_extra_residence');
            for($i = 0; $i < count($accessorials); $i++) {
                $model = Mage::getModel('servu_shipping/miscinformation_order');
                $model->setOrderId($order->getId());
                $model->setKey($accessorials[$i]);
                $model->setValue($accessorials[$i]);
                $model->save();
            }
            $order->setAccessorials($accessorials);
        }
        
        
        /*$quote = $evt->getQuote();      
        
        if($quote->getConfirmationNumbers()){
            $numbers = $quote->getConfirmationNumbers();
            $model = Mage::getModel('servu_shipping/miscinformation_order');
            $model->deleteByOrder($order->getId(),'confirmation_number');
            for($i = 0; $i < count($numbers); $i++) {
                $model = Mage::getModel('servu_shipping/miscinformation_order');
                $model->setOrderId($order->getId());
                $model->setKey('confirmation_number');
                $model->setValue($numbers[$i]);
                $model->save();
            }
            $order->setConfirmationNumbers($numbers);
        }
        
        if($quote->getAccessorials()) {
            $accessorials = $quote->getAccessorials();
            $model = Mage::getModel('servu_shipping/miscinformation_order');
            $model->deleteByOrder($quote->getId(),'s_extra_lfg');
            $model->deleteByOrder($quote->getId(),'s_extra_notify');
            $model->deleteByOrder($quote->getId(),'s_extra_residence');
            for($i = 0; $i < count($accessorials); $i++) {
                $model = Mage::getModel('servu_shipping/miscinformation_order');
                $model->setOrderId($order->getId());
                $model->setKey($accessorials[$i]);
                $model->setValue($accessorials[$i]);
                $model->save();
            }
            $order->setAccessorials($accessorials);
        }*/
    }
    
    public function loadOrderAfter($evt) {
        $order = $evt->getOrder();
        $model = Mage::getModel('servu_shipping/miscinformation_order');
        $data = $model->getByOrder($order->getId());
        $confirmationNumbers = array();
        $accessorials = array();
        foreach($data as $key => $value){
            $key = preg_replace("([-0-9])", "", $key);
            if($key === 'confirmation_number') {
                $confirmationNumbers[] = $value;
            }  
            else {
                $accessorials[] = $value;
            }
        }
        $order->setConfirmationNumbers($confirmationNumbers);
        $order->setAccessorials($accessorials);
    }
}

?>