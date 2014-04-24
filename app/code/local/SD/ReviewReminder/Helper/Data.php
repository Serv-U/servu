<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Data
 *
 * @author dustinmiller
 */
class SD_ReviewReminder_Helper_Data extends Mage_Core_Helper_Abstract { 
    public function magentoVersion() {     
        return version_compare(Mage::getVersion(), '1.4', '<');
    }  
}

?>
