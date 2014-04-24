<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Order
 *
 * @author dustinmiller
 */
class ServU_Shipping_Model_Miscinformation_Order extends Mage_Core_Model_Abstract{
	public function _construct() {
            parent::_construct();
            $this->_init('servu_shipping/order');
	}
	public function deleteByOrder($order_id,$var){
            $this->_getResource()->deleteByOrder($order_id,$var);
	}
	public function getByOrder($order_id,$var = ''){
            return $this->_getResource()->getByOrder($order_id,$var);
	}
}

?>
