<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * DISCLAIMER
 *
 *
 * @category   Primeinteractive
 * @package    Primeinteractive_Mapp
 * @version    1.0
 * @copyright   Copyright (c) 2012 Prime Interactive, Inc.
 */

class Primeinteractive_Mapp_Model_System_Config_Source_Mappexpiration {
    
    public function toOptionArray() {
        $timeframes = Mage::helper('mapp')->getExpirationTimeframes();
        
        foreach($timeframes as $key => $value){
            $template_options[] = array(
                'value' => $key,
                'label' => $value
            );
        }
        
        return $template_options;
    }
}