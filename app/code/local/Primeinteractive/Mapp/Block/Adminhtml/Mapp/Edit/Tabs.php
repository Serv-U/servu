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

class Primeinteractive_Mapp_Block_Adminhtml_Mapp_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('mapp_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mapp')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('mapp')->__('Item Information'),
          'title'     => Mage::helper('mapp')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('mapp/adminhtml_mapp_edit_tab_form')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}