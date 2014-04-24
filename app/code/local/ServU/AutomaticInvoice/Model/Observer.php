<?php
class ServU_AutomaticInvoice_Model_Observer
{
    /**
     * Mage::dispatchEvent($this->_eventPrefix.'_save_after', $this->_getEventData());
     * protected $_eventPrefix = 'sales_order';
     * protected $_eventObject = 'order';
     * event: sales_order_save_after
     */
    public function automaticallyInvoice($observer)
    {
        $order = $observer->getEvent()->getOrder();
         
        $orders = Mage::getModel('sales/order_invoice')->getCollection()
                        ->addAttributeToFilter('order_id', array('eq'=>$order->getId()));
        $orders->getSelect()->limit(1); 
         
        if ((int)$orders->count() !== 0) {
            return $this;
        }
         
        if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW) {
             
            try {
                if(!$order->canInvoice()) {
                    $order->addStatusHistoryComment('Invoicer: Order cannot be invoiced.', false);
                    $order->save(); 
                }
                 
                //START Handle Invoice
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
 
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                $invoice->register();
 
                $invoice->getOrder()->setCustomerNoteNotify(false);         
                $invoice->getOrder()->setIsInProcess(true);
                $order->addStatusHistoryComment('Automatically INVOICED.', false);
 
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
 
                $transactionSave->save();
                //END Handle Invoice
            } catch (Exception $e) {
                $order->addStatusHistoryComment('Invoicer: Exception occurred during automaticallyInvoice action. Exception message: '.$e->getMessage(), false);
                $order->save();
            }               
        }
     
    return $this;       
    }
}

?>
