<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OnePageController
 *
 * @author dustinmiller
 */
require_once 'Mage/Checkout/controllers/OnepageController.php';
class ServU_Shipping_OnepageController extends Mage_Checkout_OnepageController
{   
    
    public function updateShippingMethodAction()
    {
        $accessorials = $this->getRequest()->getParam('s_extra');
        Mage::getSingleton('core/session')->setAccessorials($accessorials);
        $this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getOnepage()->getQuote()->collectTotals()->save();
        
        $result = array();
        $result['goto_section'] = 'shipping_method';
        $result['update_section'] = array(
            'name' => 'shipping-method',
            'html' => $this->_getShippingMethodsHtml()
        );
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result)); 
    }
}
?>