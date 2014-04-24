<?php
class ServU_MediaManager_Block_Adminhtml_Files_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('files_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mediamanager')->__('File Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('mediamanager')->__('File Information'),
            'title'     => Mage::helper('mediamanager')->__('File Information'),
            'content'   => $this->getLayout()->createBlock('mediamanager/adminhtml_files_edit_tab_form')->toHtml(),
        ));

        $this->addTab('grid_section', array(
            'label'     => Mage::helper('mediamanager')->__('Related Products'),
            'title'     => Mage::helper('mediamanager')->__('Related Products'),
            'url'       => $this->getUrl('*/*/products', array('_current' => true)),
            'class'     => 'ajax',
        ));
        
        return parent::_beforeToHtml();
    }
}
