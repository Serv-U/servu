<?php

class Aero_Catalogrequest_Block_Adminhtml_Catalogrequest_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'catalogrequest';
        $this->_controller = 'adminhtml_catalogrequest';
        
        $this->_updateButton('save', 'label', Mage::helper('catalogrequest')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('catalogrequest')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('catalogrequest_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'catalogrequest_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'catalogrequest_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('catalogrequest_data') && Mage::registry('catalogrequest_data')->getId() ) {
            return Mage::helper('catalogrequest')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('catalogrequest_data')->getId()));
        } else {
            return Mage::helper('catalogrequest')->__('Add Item');
        }
    }
}