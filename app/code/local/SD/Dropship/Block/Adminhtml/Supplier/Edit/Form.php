<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Form
 *
 * @author dustinmiller
 */
class SD_Dropship_Block_Adminhtml_Supplier_Edit_Form 
    extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('dropship_supplier_form');
        $this->setTitle(Mage::helper('catalog')->__('Blah blah blah'));
    }

    /**
     * Prepare form fields
     *
     * @return Mage_Adminhtml_Block_Catalog_Search_Edit_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('sd_dropship_supplier');
        /* @var $model Mage_Dropship_Model_Supplier */

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('General Information')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        } else {
            $fieldset->addField('attribute_code', 'hidden', array('name' => 'attribute_code'));
            $fieldset->addField('option_id', 'hidden', array('name' => 'option_id'));
            $fieldset->addField('store_id', 'hidden', array('name' => 'store_id'));
        }
        
        $fieldset->addField('identifier', 'hidden', array('name' => 'identifier'));

        $fieldset->addField('address_one', 'text', array(
            'name'      => 'address_one',
            'label'     => Mage::helper('catalog')->__('Address(1)'),
            'title'     => Mage::helper('catalog')->__('Address(1)'),
            'required'  => true,
        ));
        
        $fieldset->addField('address_two', 'text', array(
            'name'      => 'address_two',
            'label'     => Mage::helper('catalog')->__('Address(2)'),
            'title'     => Mage::helper('catalog')->__('Address(2)'),
        ));
        
        $fieldset->addField('city', 'text', array(
            'name'      => 'city',
            'label'     => Mage::helper('catalog')->__('City'),
            'title'     => Mage::helper('catalog')->__('City'),
            'required'  => true,
        ));
        
        $fieldset->addField('state', 'text', array(
            'type'      => 'text',
            'input'     => 'select',
            'name'      => 'state',
            'label'     => Mage::helper('catalog')->__('State'),
            'title'     => Mage::helper('catalog')->__('State'),
            'required'  => true,
            'source'    => 'sd/supplier_source_states',
        ));

        $fieldset->addField('zip_code', 'text', array(
            'name'      => 'zip_code',
            'label'     => Mage::helper('catalog')->__('Zip Code'),
            'title'     => Mage::helper('catalog')->__('Zip Code'),
            'required'  => true,
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
?>