<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml order items grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SD_Acm_Block_Adminhtml_Individual_Sales_Order_View_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        $id = $this->getRequest()->getParam('id');
        $quote_id = Mage::getModel('sd_acm/acm')->load($id)->getData('quote_id');
        $quote = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter('entity_id',$quote_id)->getFirstItem();

        //Set Item statuses
        $this->_setItemStatuses($quote);

        //Set Billing Address
        $billing_address = $this->_getBillingAddress($quote);
        $quote->setData('billing_address',$billing_address);
        
        //Set Shipping Address
        $shipping_address = $this->_getShippingAddress($quote, $billing_address);
        $quote->setData('shipping_address',$shipping_address);
        
        $this->setQuote($quote);
        parent::_beforeToHtml();
    }
    
    /**
     * Retrieve Item Statuses
     */
    private function _setItemStatuses($quote){
        //Get Order Status
        $quote_id = $quote->getData('entity_id');

        $order = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('quote_id', $quote_id)->getFirstItem();
        $status = $order->getData('status');
        if(empty($status)) $status = 'abandoned';
        
        //Check quote items against order items
        $quote_items = $quote->getAllItems();
        $order_items = $order->getAllItems();
        foreach($quote_items as $quote_item){
            $setStatus = false;
            $quote_sku = $quote_item->getData('sku');
            foreach($order_items as $order_item){
                $order_sku = $order_item->getData('sku');
                if($order_sku == $quote_sku){
                    $setStatus = true;
                }
            }
            if($setStatus == false && !empty($order_items)){
                $status = 'removed';
            }
            $quote_item->setData('status',$status);
        }
    }
    
    /**
     * Retrieve Quote or Customer Billing Address
     */
    private function _getBillingAddress($quote){
        if($quote->getBillingAddress()->getData('city')){
            return $quote->getBillingAddress()->getFormated(true);
        }
        if($customer_id = $quote->getData('customer_id')){
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            if($customer->getDefaultBillingAddress()){
                return $customer->getDefaultBillingAddress()->format('html');
            }
        }
        return '<em class="error">Unable to retrieve address information.</em>';
    }
    
    /**
     * Retrieve Quote or Customer Shipping Address
     */
    private function _getShippingAddress($quote, $billing_address){
        if($quote->getShippingAddress()->getData('city')){
            return $quote->getShippingAddress()->getFormated(true);
        }
        if($customer_id = $quote->getData('customer_id')){
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            if($customer->getDefaultShippingAddress()){
                return $customer->getDefaultShippingAddress()->format('html');
            }
        }
        return $billing_address;
    }
}