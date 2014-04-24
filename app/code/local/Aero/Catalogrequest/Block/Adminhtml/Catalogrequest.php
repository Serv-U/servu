<?php
class Aero_Catalogrequest_Block_Adminhtml_Catalogrequest extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_catalogrequest';
    $this->_blockGroup = 'catalogrequest';
    $this->_headerText = Mage::helper('catalogrequest')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('catalogrequest')->__('Add Item');
    parent::__construct();
  }
}