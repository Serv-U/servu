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

class Primeinteractive_Mapp_Model_Mysql4_Mapp_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mapp/mapp');
    }
}