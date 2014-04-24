<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductFiles
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Model_Mysql4_Productfiles extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {    
        $this->_init('mediamanager/productfiles', 'id');  
    }
        public function addGridPosition($collection,$manager_id){
/*
            $table2 = $this->getMainTable();
            $cond = $this->_getWriteAdapter()->quoteInto('e.entity_id = t2.product_id','');
            $collection->getSelect()->joinLeft(array('t2'=>$table2), $cond);
            $collection->getSelect()->group('e.entity_id');
            echo $collection->getSelect();
*/    
    }
}

?>
