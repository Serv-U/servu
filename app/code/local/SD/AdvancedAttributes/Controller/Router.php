<?php
class SD_AdvancedAttributes_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();

        $filters = new SD_advancedattributes_Controller_Router();
        $front->addRouter('advancedattributes', $filters);
    }
    
    public function match(Zend_Controller_Request_Http $request)
    {
        return false;
    }
}
?>