<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Quote
 *
 * @author dustinmiller
 */
class ServU_Shipping_Model_Miscinformation_Quote extends Mage_Core_Model_Abstract{
	public function _construct()
	{
            parent::_construct();
            $this->_init('servu_shipping/quote');
	}
	public function deleteByQuote($quote_id,$var){
            $this->_getResource()->deleteByQuote($quote_id,$var);
	}
	public function getByQuote($quote_id,$var = ''){
            return $this->_getResource()->getByQuote($quote_id,$var);
	}
}

?>
