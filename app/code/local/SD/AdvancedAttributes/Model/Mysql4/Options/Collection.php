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
class SD_AdvancedAttributes_Model_Mysql4_Options_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advancedattributes/options');    
    }
    
    protected function _initSelect()
    {
    	parent::_initSelect();
        
        
        if(Mage::registry('advancedattributes_id')) {
            $this->getSelect()
                ->joinright(array('eao' => $this->getTable('eav/attribute_option')), 'eao.option_id = main_table.option_id', array())
                ->join(array('eaov' => $this->getTable('eav/attribute_option_value')), 'eaov.option_id = eao.option_id', array('option_id', 'value'))
                ->where('eao.attribute_id = ?', Mage::registry('advancedattributes_id'))
                ->order('eaov.value');
       }  
       
       
        return $this;
    }
}

?>
