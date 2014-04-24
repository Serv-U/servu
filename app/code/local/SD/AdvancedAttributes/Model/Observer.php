<?php
class SD_AdvancedAttributes_Model_Observer {
    
    public function processPreDispatch(Varien_Event_Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        //Mage::log(get_class($action)); 
        // Check to see if $action is a Product controller
        if ($action instanceof SD_AdvancedAttributes_CategoryController ||
                $action instanceof SD_AdvancedAttributes_PageController ||
                $action instanceof SD_AdvancedAttributes_ResultController) {
            $cache = Mage::app()->getCacheInstance();
            // Tell Magento to 'ban' the use of block_html for this request
            $cache->banUse('block_html');
        }
    }
}

?>
