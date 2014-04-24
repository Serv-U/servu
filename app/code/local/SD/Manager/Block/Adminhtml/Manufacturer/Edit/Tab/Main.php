<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Main
 *
 * @author dustinmiller
 */
class SD_Manager_Block_Adminhtml_Manufacturer_Edit_Tab_Main
	extends Mage_Adminhtml_Block_Widget_Form
//	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _prepareForm()
    {
        $model = Mage::registry('sd_manager_manufacturer');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('adminhtml')->__('General Information'),'class'=>'fieldset-wide'));

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
        
        $hint = 'domain.com/';
        if($_ac = $model->getAttributeCode()) {
        	$hint .= $_ac.'/';
        }
        $hint .= 'url_key';
        
        $fieldset->addField('url_key', 'text', array(
            'name'      => 'url_key',
            'label'     => Mage::helper('adminhtml')->__('URL Key'),
            'title'     => Mage::helper('adminhtml')->__('URL Key'),
            'required'  => true,
            'class'     => 'validate-url_key',
            'after_element_html' => '<p class="nm"><small>' . Mage::helper('adminhtml')->__('(eg: '.$hint.')') . '</small></p>',
        ));

    	$fieldset->addField('is_enabled', 'select', array(
            'label'     => Mage::helper('adminhtml')->__('Status'),
            'title'     => Mage::helper('adminhtml')->__('Status'),
            'name'      => 'is_enabled',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('adminhtml')->__('Enabled'),
                '0' => Mage::helper('adminhtml')->__('Disabled'),
            ),
            'after_element_html' => '<p class="nm"><small>' . Mage::helper('adminhtml')->__('Disabled manufacturers will not appear in the list, and will not have a link on them in the product pages') . '</small></p>',
        ));
        
        $fieldset->addField('is_featured', 'select', array(
            'label'     => Mage::helper('adminhtml')->__('Featured Manufacturer'),
            'title'     => Mage::helper('adminhtml')->__('Featured Manufacturer'),
            'name'      => 'is_featured',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('adminhtml')->__('Yes'),
                '0' => Mage::helper('adminhtml')->__('No'),
            ),
            'after_element_html' => '<p class="nm"><small>' . Mage::helper('adminhtml')->__('Featured Manufacturers will show up in the home page block and on top of the "Shop By Manufacturer" page') . '</small></p>',
        ));

    	$fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'label'     => Mage::helper('adminhtml')->__('Sort Order'),
            'title'     => Mage::helper('adminhtml')->__('Sort Order'),
            'required'  => false,
            'width'     => '50px',
            'after_element_html' => '<p class="nm"><small>' . Mage::helper('adminhtml')->__('This field orders the featured manufacturers. Lower numbers appear first. Duplicate values are sorted alphabetically.') . '</small></p>',
        ));

        $fieldset->addField('logo', 'image', array(
            'name'      => 'logo',
            'label'     => Mage::helper('adminhtml')->__('Logo'),
            'title'     => Mage::helper('adminhtml')->__('Logo'),
            'required'  => false,
        	'value' => null,
            'after_element_html' => '<p class="nm"><small>' . Mage::helper('adminhtml')->__('The logo will display in the list and product pages') . '</small></p>',
        ));

    	$fieldset->addField('description', 'editor', array(
            'name'      => 'description',
            'label'     => Mage::helper('adminhtml')->__('Description'),
            'title'     => Mage::helper('adminhtml')->__('Description'),
            'style'     => 'height:20em;',
            'wysiwyg'   => true,
            'config'    => Mage::getVersion() > '1.4' ? @Mage::getSingleton('cms/wysiwyg_config')->getConfig() : false,
            'required'  => false,
        ));
        
        //DM 11-26-2013 Added simple banner functionality
        $fieldset->addField('banner', 'editor', array(
            'name'      => 'banner',
            'label'     => Mage::helper('adminhtml')->__('Banner'),
            'title'     => Mage::helper('adminhtml')->__('Banner'),
            'style'     => 'height:15em;',
            'wysiwyg'   => true,
            'config'    => Mage::getVersion() > '1.4' ? @Mage::getSingleton('cms/wysiwyg_config')->getConfig() : false,
            'required'  => false,
        ));


        //fix the image upload nag
        $values = $model->getData();
        if (is_array($values) && isset($values['logo'])) {
	        if (is_array($values['logo']) && isset($values['logo']['value'])) {
				$values['logo'] = 'catalog/manufacturers/'.$values['logo']['value'];
			} elseif (is_string($values['logo']) && ($values['logo'] > '')) {
				$values['logo'] = 'catalog/manufacturers/'.$values['logo'];
			}
        }
        $form->addValues($values);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}

?>