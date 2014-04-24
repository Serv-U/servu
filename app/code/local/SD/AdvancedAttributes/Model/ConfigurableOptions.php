<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Options
 *
 * @author dustinmiller
 */
class SD_AdvancedAttributes_Model_ConfigurableOptions extends Mage_Core_Model_Abstract{
    public function _construct(){
        parent::_construct();
        $this->_init('advancedattributes/configurableOptions');
    }
    
    public function loadFromOptionId($option_id) {
        $this->_getResource()->loadFromOptionId($this, $option_id);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
    
}

?>
