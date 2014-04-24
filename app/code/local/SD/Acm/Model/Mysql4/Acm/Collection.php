<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*SELECT * FROM sd_carts_mailed 
right join sales_flat_quote on sd_carts_mailed.quote_id = sales_flat_quote.entity_id 
where sales_flat_quote.is_active = 1 and sales_flat_quote.customer_id <> '' or sd_carts_mailed.status <> '3'*/

class SD_Acm_Model_Mysql4_Acm_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sd_acm/acm');  
    }
    
    public function individualMerge()
    {
        $this->getSelect()
        	->joinLeft(array('so' => $this->getTable('sales/order')), 'so.quote_id = main_table.quote_id', array('so.base_subtotal', 'so.created_at', 'so.entity_id'))
                ->joinLeft(array('sq' => $this->getTable('sales/quote')), 'sq.entity_id = main_table.quote_id', array('customer_email', 'customer_firstname', 'customer_lastname'))
                ->group('main_table.id');
                //->order('main_table.quote_id');
        return $this;
    }
    
}

?>
