<?php
class SD_Acm_Block_Newsletter extends Mage_Customer_Block_Newsletter
{
    
    public function getIsNotified()
    {   
        $notified = false;
        $acmModel = Mage::getModel('sd_acm/unsubscribe')->loadCustomerId(
                Mage::getSingleton('customer/session')->getCustomer()->getEntityId(), 
                Mage::app()->getStore()->getStoreId());

        if($acmModel->getId() == '' || $acmModel->getIsActive()) {
            $notified = true;
        }
        
        return $notified;
    }

}
