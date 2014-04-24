<?php

class SD_Acm_Block_Adminhtml_Individual_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $model = Mage::registry('acm_data');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('adminhtml/individual/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('display_form', array('legend' => Mage::helper('sd_acm')->__('Notes')));
        
        $fieldset->addField('yesnoContacted', 'select', array(
            'name'      => 'yesnoContacted',
            'label'     => Mage::helper('adminhtml')->__('Contacted'),
            'title'     => Mage::helper('adminhtml')->__('Contacted'),
            'required'  => false,
            'values' => array('0' => 'No','1' => 'Yes'),
            'tabindex' => 1
        ));

        $fieldset->addField('comments', 'textarea', array(
            'name'      => 'comments',
            'label'     => Mage::helper('adminhtml')->__('Comments'),
            'title'     => Mage::helper('adminhtml')->__('Comments'),
            'required'  => false,
            'tabindex' => 2
        ));

        $values = $model->getData();
        $form->setValues($values);
        return parent::_prepareForm();
    }

}
