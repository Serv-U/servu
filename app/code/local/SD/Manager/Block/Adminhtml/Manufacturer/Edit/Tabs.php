<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tabs
 *
 * @author dustinmiller
 */
class SD_Manager_Block_Adminhtml_Manufacturer_Edit_Tabs 
    extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('manufacturer_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Manufacturer Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('adminhtml')->__('General Information'),
            'title'     => Mage::helper('adminhtml')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('sd_manager/adminhtml_manufacturer_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('meta_section', array(
            'label'     => Mage::helper('adminhtml')->__('Meta Data'),
            'title'     => Mage::helper('adminhtml')->__('Meta Data'),
            'content'   => $this->getLayout()->createBlock('sd_manager/adminhtml_manufacturer_edit_tab_meta')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}

?>