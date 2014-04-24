<?php


class SD_AdvancedAttributes_Block_Manage_Configurables_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'advancedattributes';
        $this->_controller = 'manage_configurables';
        
        $this->_updateButton('save', 'label', Mage::helper('advancedattributes')->__('Save'));
        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getBackUrl() . '\')');
        $this->_removeButton('delete');

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('advancedattributes_data') && Mage::registry('advancedattributes_data')->getAttributeCode() ) {
            return Mage::helper('advancedattributes')->__("Manage Configurable '%s'", $this->htmlEscape(Mage::registry('advancedattributes_data')->getFrontendLabel()));
        }
        else {
            return Mage::helper('advancedattributes')->__('Edit Configurable Properties');
        }
    }
}
