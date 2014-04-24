<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SD_ReviewReminder_Model_Mysql4_ReviewReminder_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('reviewreminder/reviewReminder');  
    }
    
    public function mergeWithOrder()
    {
        $this->getSelect()
        	->joinLeft(array('sales_order' => $this->getTable('sales/order')), 'sales_order.entity_id = main_table.order_id', 
                        array('sales_order.status', 'sales_order.base_subtotal', 'sales_order.created_at', 'sales_order.increment_id', 'sales_order.entity_id', 
                            'sales_order.customer_firstname', 'sales_order.customer_lastname', 'sales_order.customer_email'))
                ->where('main_table.id >= 0');
        return $this;
    }
    
    public function couponsUsed() {
        $this->getSelect()
                ->joinRight(array('sales_order' => $this->getTable('sales/order')), 'sales_order.coupon_code = main_table.coupon_code', 
                        array('sales_order.increment_id'))
                ->where("main_table.coupon_code != ''");
    }
    
}

?>
