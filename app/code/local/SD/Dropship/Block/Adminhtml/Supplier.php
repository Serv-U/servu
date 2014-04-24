<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Supplier
 *
 * @author dustinmiller
 */
class SD_Dropship_Block_Adminhtml_Supplier 
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_supplier';
        $this->_blockGroup = 'sd_dropship';
        $this->_headerText = Mage::helper('sd_dropship')->__('Manage Suppliers');
        parent::__construct();
        $this->_removeButton('add');
    }
}

?>
