<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SD_ReviewReminder_Model_Mysql4_Emails_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('reviewreminder/emails');  
    }
    
     public function retrieveSentCollection()
    {    
        $this->getSelect()
                ->join(array('so' => $this->getTable('sales/order')), 'so.entity_id = main_table.order_id', array('customer_email', 'customer_firstname', 'customer_lastname'))
                ->where('main_table.id >= 0');

        return $this;
    }
    
}

?>
