<?php


class SD_AdvancedAttributes_Block_Manage_Options_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'advancedattributes';
        $this->_controller = 'manage_options';
        
        $this->_updateButton('save', 'label', Mage::helper('advancedattributes')->__('Save Option'));
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
        if( Mage::registry('advancedattributes_options_data') && Mage::registry('advancedattributes_options_data')->getValue()) {
            return Mage::helper('advancedattributes')->__("Manage Option '%s'", $this->htmlEscape(Mage::registry('advancedattributes_options_data')->getValue()));
        }
        else {
            return Mage::helper('advancedattributes')->__('Edit Option Properties');
        }
    }
    
    public function getBackUrl()
    {
        if ($this->getRequest()->getParam('attribute_filter_id')) {
            return $this->getUrl('*/manage_filters/edit', array('id'  => $this->getRequest()->getParam('attribute_filter_id'), 'attribute_id'=> $this->getRequest()->getParam('attribute_id')));
        }
        else {
            return $this->getUrl('*/manage_filters/edit', array('attribute_code'  => $this->getRequest()->getParam('attribute_code'), 'attribute_id'=> $this->getRequest()->getParam('attribute_id')));
        }   
    }
}
