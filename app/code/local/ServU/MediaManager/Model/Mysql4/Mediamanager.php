<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mediamanager
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Model_Mysql4_Mediamanager extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {    
        $this->_init('mediamanager/mediamanager', 'id');
    }
    
    public function load(Mage_Core_Model_Abstract $object, $value, $field=null) {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
			$select = $read->select()
				->from(array('main_table' => $this->getMainTable()))
				->joinLeft(array('ea' => $this->getTable('mediamanager/products')), 'ea.file_id = '.'main_table.id ', array('file_id', 'product_id'))
                                //->join(array('cea' => $this->getTable('catalog/eav_file')), 'cea.file_id = ea.file_id', array())
                                ->where('main_table.id = ?', $value);

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data); 
            }
        }
        $this->_afterLoad($object);

        return $this;   
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object) {
        $select = parent::_getLoadSelect($field, $value, $object);
        
        $select->joinRight(array('ea' => $this->getTable('eav/file')), 'ea.file_id = '.$this->getMainTable().'.file_id ', array('frontend_label', 'file_code', 'file_id'))
                ->join(array('cea' => $this->getTable('catalog/eav_file')), 'cea.file_id = ea.file_id', array())
                ->order('frontend_label')
                ->orwhere('cea.file_id = ?', $value);
        
        return $select;
    }

    public function addGridPosition($collection,$manager_id){
//            $table2 = $this->getMainTable();
//            $cond = $this->_getWriteAdapter()->quoteInto('e.entity_id = t2.customer_id','');
//            $collection->getSelect()->joinLeft(array('t2'=>$table2), $cond);
//            $collection->getSelect()->group('e.entity_id');
            //echo $collection->getSelect();
    }
}

?>
