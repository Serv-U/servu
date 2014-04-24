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
class SD_AdvancedAttributes_Model_Advancedattributes extends Mage_Core_Model_Abstract {
    
    public function _construct(){
        parent::_construct();
        $this->_init('advancedattributes/advancedattributes');
    }
    
    public function loadFromAttributeId($attribute_id)
    {
        $this->_getResource()->loadFromAttributeId($this, $attribute_id);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
    
}

?>
