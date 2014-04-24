<?php


class SD_AdvancedAttributes_Block_Manage_Options_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/manage_options/save', array('option_id' => $this->getRequest()->getParam('option_id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        
        $model = Mage::registry('advancedattributes_options_data');
        
        $fieldset = $form->addFieldset('general_form', array('legend' => Mage::helper('advancedattributes')->__('General')));
        
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        } else {
            $fieldset->addField('attribute_id', 'hidden', array('name' => 'attribute_id'));
            $fieldset->addField('option_id', 'hidden', array('name' => 'option_id'));
        }

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        
        $fieldset->addField('is_featured', 'select', array(
            'name' => 'is_featured',
            'label' => Mage::helper('advancedattributes')->__('Featured'),
            'values' => $yesnoSource,
        ), 'apply_to');
        
        $fieldset = $form->addFieldset('products_list', array('legend' => Mage::helper('advancedattributes')->__('Products List Page')));
        
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('advancedattributes')->__('Title'),
            'title' => Mage::helper('advancedattributes')->__('Title'),
            'name' => 'title',
            'style' => 'width: 420px;',
        ));
        
        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('advancedattributes')->__('Description'),
            'title' => Mage::helper('advancedattributes')->__('description'),
            'name' => 'description',
            'style' => 'width: 420px;',
        ));
        
        $fieldset->addField('cms_block', 'text', array(
            'label' => Mage::helper('advancedattributes')->__('CMS Block'),
            'title' => Mage::helper('advancedattributes')->__('CMS Block'),
            'name' => 'cms_block',
            'style' => 'width: 420px;',
        ));
        
        $fieldset->addField('title_tag', 'text', array(
            'label' => Mage::helper('advancedattributes')->__('Title Tag'),
            'title' => Mage::helper('advancedattributes')->__('Title Tag'),
            'name' => 'title_tag',
            'style' => 'width: 420px;',
        ));
        
        $fieldset->addField('meta_tag', 'text', array(
            'label' => Mage::helper('advancedattributes')->__('Meta Tag'),
            'title' => Mage::helper('advancedattributes')->__('Meta Tag'),
            'name' => 'meta_tag',
            'style' => 'width: 420px;',
        ));
        
        $fieldset->addField('product_list_image', 'image', array(
            'name'      => 'product_list_image',
            'label'     => Mage::helper('adminhtml')->__('Image'),
            'title'     => Mage::helper('adminhtml')->__('Image'),
            'required'  => false,
        	'value' => null,
        ));
        
        $fieldset = $form->addFieldset('product_view_form', array('legend' => Mage::helper('advancedattributes')->__('Product View Page')));
        
        $fieldset->addField('product_view_image', 'image', array(
            'name'      => 'product_view_image',
            'label'     => Mage::helper('adminhtml')->__('Image'),
            'title'     => Mage::helper('adminhtml')->__('Image'),
            'required'  => false,
        	'value' => null,
        ));
        
        $fieldset = $form->addFieldset('layered_navigation_form', array('legend' => Mage::helper('advancedattributes')->__('Layered Navigation')));
        
        $fieldset->addField('layered_image', 'image', array(
            'name'      => 'layered_image',
            'label'     => Mage::helper('adminhtml')->__('Image'),
            'title'     => Mage::helper('adminhtml')->__('Image'),
            'required'  => false,
        	'value' => null,
        ));
        
        if ( Mage::registry('advancedattributes_options_data') ) {
            $form->setValues(Mage::registry('advancedattributes_options_data')->getData());
        }
        
        $values = $model->getData();
        
        if (is_array($values) && isset($values['product_list_image'])) {
            if (is_array($values['product_list_image']) && isset($values['product_list_image']['value'])) {
                $values['product_list_image'] = 'catalog/attributes/list/'.$values['product_list_image']['value'];
            } elseif (is_string($values['product_list_image']) && ($values['product_list_image'] > '')) {
                $values['product_list_image'] = 'catalog/attributes/list/'.$values['product_list_image'];
            }
        }
        
        if (is_array($values) && isset($values['product_view_image'])) {
            if (is_array($values['product_view_image']) && isset($values['product_view_image']['value'])) {
                $values['product_view_image'] = 'catalog/attributes/view/'.$values['product_view_image']['value'];
            } elseif (is_string($values['product_view_image']) && ($values['product_view_image'] > '')) {
                $values['product_view_image'] = 'catalog/attributes/view/'.$values['product_view_image'];
            }
        }
        
        if (is_array($values) && isset($values['layered_image'])) {
            if (is_array($values['layered_image']) && isset($values['layered_image']['value'])) {
                $values['layered_image'] = 'catalog/attributes/layered/'.$values['layered_image']['value'];
            } elseif (is_string($values['layered_image']) && ($values['layered_image'] > '')) {
                $values['layered_image'] = 'catalog/attributes/layered/'.$values['layered_image'];
            }
        }
        $form->addValues($values);
        
        return parent::_prepareForm();
    }
}
