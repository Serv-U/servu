<?php

class Aero_Catalogrequest_Block_Adminhtml_Catalogrequest_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('catalogrequest_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('catalogrequest')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('catalogrequest')->__('Item Information'),
          'title'     => Mage::helper('catalogrequest')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('catalogrequest/adminhtml_catalogrequest_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}