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
class SD_AdvancedAttributes_Model_Mysql4_Configurables_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advancedattributes/configurables');    
    }
    
    protected function _initSelect()
    {
    	parent::_initSelect();
        
        $this->getSelect()
                ->joinRight(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = main_table.attribute_id ', array('frontend_label', 'attribute_code', 'attribute_id'))
                ->join(array('cea' => $this->getTable('catalog/eav_attribute')), 'cea.attribute_id = ea.attribute_id', array())
                ->where('cea.is_configurable > ?', '0')
                ->where('ea.source_model = ?', 'eav/entity_attribute_source_table')
                ->order('frontend_label');

        return $this;
    }
    
    public function addNoAttributeIdFilter()
    {
        $this->getSelect()
        	->where('main_table.attribute_id != ?', '');
        return $this;
    }
    
    public function addAttributeIdFilter()
    {
        $this->getSelect()
        	->where('main_table.attribute_id IS NULL');
        return $this;
    }
}

?>
