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

class Primeinteractive_Mapp_Block_Mapp extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

     public function getMapp()
     {
        if (!$this->hasData('mapp')) {
            $this->setData('mapp', Mage::registry('mapp'));
        }
        return $this->getData('mapp');

    }
}