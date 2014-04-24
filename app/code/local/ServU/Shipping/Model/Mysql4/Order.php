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
class ServU_Shipping_Model_Mysql4_Order extends Mage_Core_Model_Mysql4_Abstract{
	
    public function _construct() {
        $this->_init('servu_shipping/order', 'id');
    }
    
    public function deleteByOrder($order_id,$var) {
        $table = $this->getMainTable();
        $where = $this->_getWriteAdapter()->quoteInto('order_id = ? AND ', $order_id)
        .$this->_getWriteAdapter()->quoteInto('`key` = ? 	', $var);
        $this->_getWriteAdapter()->delete($table,$where);
    }
    
    public function getByOrder($order_id,$var = '') {
        $table = $this->getMainTable();
        $where = $this->_getReadAdapter()->quoteInto('order_id = ?', $order_id);
        if(!empty($var)){
                $where .= $this->_getReadAdapter()->quoteInto(' AND `key` = ? ', $var);
        }
        $sql = $this->_getReadAdapter()->select()->from($table)->where($where);
        $rows = $this->_getReadAdapter()->fetchAll($sql);
        $return = array();
        $count = 0;
        foreach($rows as $row){
                $return[$row['key'].'-'.$count] = $row['value'];
                $count++;
        }
        return $return;
    }
}
?>
