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

class Primeinteractive_Mapp_Model_System_Config_Source_Mappemails {
    
    public function toOptionArray() {
        $templates = Mage::getModel('core/email_template')->getCollection();
        
        foreach($templates as $template){
            $template_options[] = array(
                'value' => $template->getData('template_id'),
                'label'=>Mage::helper('adminhtml')->__($template->getData('template_code'))
            );
        }
        
        return $template_options;
    }
}