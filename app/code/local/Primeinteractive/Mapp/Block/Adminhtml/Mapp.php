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

class Primeinteractive_Mapp_Block_Adminhtml_Mapp extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_mapp';
    $this->_blockGroup = 'mapp';
    $this->_headerText = Mage::helper('mapp')->__('Manage Mapp Posting');
    $this->_addButtonLabel = Mage::helper('mapp')->__('Add Item');
    parent::__construct();
  }
}