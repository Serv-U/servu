<?php


class SD_Acm_Block_Adminhtml_Individual_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'acm';
        $this->_controller = 'adminhtml_individual';
        
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getBackUrl() . '\')');
        $this->_removeButton('delete');

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('acm_data') && $id = Mage::registry('acm_data')->getData('id') ) {
            $individual = Mage::getModel('sd_acm/acm')
                                ->getCollection()
                                ->addFieldToFilter('id',$id)
                                ->individualMerge()
                                ->getFirstItem();
            $detail = $individual['customer_email'] . ' (' . $individual['abandoned_date'] . ')';
            
            return Mage::helper('sd_acm')->__("%s Abandoned Cart", $this->htmlEscape($detail));
        }
        else {
            return Mage::helper('sd_acm')->__('Edit Individual Abandoned Cart');
        }
    }
}
