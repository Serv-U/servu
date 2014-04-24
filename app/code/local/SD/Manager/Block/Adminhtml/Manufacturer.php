<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manufacturer
 *
 * @author dustinmiller
 */
class SD_Manager_Block_Adminhtml_Manufacturer 
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_manufacturer';
        $this->_blockGroup = 'sd_manager';
        $this->_headerText = Mage::helper('sd_manager')->__('Manage Manufacturer');
        parent::__construct();
        $this->_removeButton('add');
    }
}

?>