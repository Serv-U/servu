<?php


class SD_AdvancedAttributes_Block_Manage_Filters_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('filters_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('advancedattributes')->__('Filter Properties'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('advancedattributes')->__('General'),
            'title'     => Mage::helper('advancedattributes')->__('General'),
            'content'   => $this->getLayout()->createBlock('advancedattributes/manage_filters_edit_tab_form')->toHtml(),
        ));

        $this->addTab('options_section', array(
            'label'     => Mage::helper('advancedattributes')->__('Options'),
            'title'     => Mage::helper('advancedattributes')->__('Options'),
            'url'       => $this->getUrl('*/*/options', array('_current' => true)),
            'class'     => 'ajax',
         ));
        /*$this->addTab('options_section', array(
        'label'     => Mage::helper('advancedattributes')->__('Options'),
        'title'     => Mage::helper('advancedattributes')->__('Options'),
        'content'   => $this->getLayout()->createBlock('advancedattributes/manage_filters_edit_tab_options')->toHtml(),
        ));*/

        return parent::_beforeToHtml();
    }
}
