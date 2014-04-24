<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Meta
 *
 * @author dustinmiller
 */
class SD_Manager_Block_Adminhtml_Manufacturer_Edit_Tab_Meta 
    extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    public function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $model = Mage::registry('sd_manager_manufacturer');

        $fieldset = $form->addFieldset('design_fieldset', array(
            'legend' => Mage::helper('sd_manager')->__('Meta Data'),
            'class'  => 'fieldset-wide',
        ));

    	$fieldset->addField('page_title', 'text', array(
            'name'      => 'page_title',
            'label'     => Mage::helper('adminhtml')->__('Page Title'),
            'title'     => Mage::helper('adminhtml')->__('Page Title'),
        ));

        $fieldset->addField('meta_keywords', 'editor', array(
            'name' => 'meta_keywords',
            'label' => Mage::helper('sd_manager')->__('Meta Keywords'),
            'title' => Mage::helper('sd_manager')->__('Meta Keywords'),
        ));

    	$fieldset->addField('meta_description', 'editor', array(
            'name' => 'meta_description',
            'label' => Mage::helper('sd_manager')->__('Meta Description'),
            'title' => Mage::helper('sd_manager')->__('Meta Description'),
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}

?>