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
class SD_AdvancedAttributes_Model_Mysql4_ConfigurableOptions_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advancedattributes/configurableOptions');
    }
}

?>
