<?php

class Aero_Catalogrequest_Model_Observer
{
    
    public function checkRobot($observer) {
        $controller = $observer->getControllerAction();
        
        $request = $controller->getRequest();

        if ($request->getPost('confirm-email') != '') {
            Mage::getSingleton('core/session')->addError('Incorrect Response');
            $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            Mage::getSingleton('customer/session')
                ->setCustomerFormData($controller->getRequest()->getPost());
            $controller->getResponse()->setRedirect(Mage::getUrl('*/*'));
        }

        return $this;
    }
}
    