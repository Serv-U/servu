<?php
class ServU_Emailtracking_Model_Observer
{
    /**
     * Mage::dispatchEvent($this->_eventPrefix.'_save_after', $this->_getEventData());
     * protected $_eventPrefix = 'sales_order';
     * protected $_eventObject = 'order';
     * event: sales_order_save_after
     */
    public function emails() {
        $shipment_collection = Mage::getResourceModel('sales/order_shipment_collection');
        //$shipment_collection->addAttributeToFilter('created_at', array('gt' => date("Y-m-d H:i:s", (time() - (1 * 1 * 25 * 60)))));
        $shipment_collection->addAttributeToFilter('email_sent', array('null' => true));

        foreach($shipment_collection as $sc) {
            $shipment = Mage::getModel('sales/order_shipment');
            $shipment->load($sc->getId());
            //Mage::log($shipment);
            if($shipment){
                if(!$shipment->getEmailSent()) {
                    $shipment->sendEmail(true);
                    $shipment->setEmailSent(true);
                    $shipment->save();                          
                }
            } 
        } 
    }
}

?>
