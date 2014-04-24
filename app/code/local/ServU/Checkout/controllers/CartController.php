<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CartController
 *
 * @author dustinmiller
 */
require_once 'Mage/Checkout/controllers/CartController.php';
class ServU_Checkout_CartController extends Mage_Checkout_CartController
{   
    public function estimateUpdatePostAction()
    {
        $code = (string) $this->getRequest()->getParam('estimate_method');
        $accessorials = $this->getRequest()->getParam('s_extra');
        $session = Mage::getSingleton('core/session');
        $session->setData('accessorials', $accessorials);

        if (!empty($code)) {
            $this->_getQuote()->getShippingAddress()->setShippingMethod($code)/*->collectTotals()*/->save();
        }
        $this->_goBack();
    }
}?>