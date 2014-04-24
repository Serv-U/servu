<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of advancedattributes
 *
 * @author dustinmiller
 */
class SD_AdvancedAttributes_Model_Mysql4_Advancedattributes extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {    
        $this->_init('advancedattributes/advancedattributes', 'id');
    }
    
    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
			$select = $read->select()
				->from(array('main_table' => $this->getMainTable()))
				->joinLeft(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = '.'main_table.attribute_id ', array('frontend_label', 'attribute_code', 'attribute_id'))
                                ->join(array('cea' => $this->getTable('catalog/eav_attribute')), 'cea.attribute_id = ea.attribute_id', array())
                                ->where('main_table.id = ?', $value);

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data); 
            }
        }
        $this->_afterLoad($object);

        return $this;   
    }
    
    public function loadFromAttributeId(Mage_Core_Model_Abstract $object, $value)
    {
     $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
			$select = $read->select()
				->from(array('main_table' => $this->getMainTable()))
				->joinRight(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = '.'main_table.attribute_id ', array('frontend_label', 'attribute_code', 'attribute_id'))
                                ->join(array('cea' => $this->getTable('catalog/eav_attribute')), 'cea.attribute_id = ea.attribute_id', array())
                                ->where('ea.attribute_id = ?', $value);

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
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        
        $select->joinRight(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = '.$this->getMainTable().'.attribute_id ', array('frontend_label', 'attribute_code', 'attribute_id'))
                ->join(array('cea' => $this->getTable('catalog/eav_attribute')), 'cea.attribute_id = ea.attribute_id', array())
                ->order('frontend_label')
                ->orwhere('cea.attribute_id = ?', $value);
        
        return $select;
    }
    
}

?>
