<?php

class SD_AdvancedAttributes_Block_Manage_Filters_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $model = Mage::registry('advancedattributes_data');
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('display_form', array('legend' => Mage::helper('advancedattributes')->__('Display Properties')));
        
        if ($model->getId()) {
        	$fieldset->addField('id', 'hidden', array('name' => 'id'));
        } else {
        	$fieldset->addField('attribute_id', 'hidden', array('name' => 'attribute_id'));
                $fieldset->addField('attribute_code', 'hidden', array('name' => 'attribute_code'));
        }
        
        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        
        $fieldset->addField('display_type', 'select', array(
            'label' => Mage::helper('advancedattributes')->__('Display Type'),
            'name' => 'display_type',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('advancedattributes')->__('Labels Only'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('advancedattributes')->__('Images Only'),
                ),
                array(
                    'value' => 3,
                    'label' => Mage::helper('advancedattributes')->__('Images and Labels'),
                ),
                array(
                    'value' => 4,
                    'label' => Mage::helper('advancedattributes')->__('Dropdown List'),
                ),
            ),
        ));
        
        $fieldset->addField('unfolded_options', 'text', array(
            'label' => Mage::helper('advancedattributes')->__('Number of unfolded options'),
            'title' => Mage::helper('advancedattributes')->__('Number of unfolded options'),
            'name' => 'unfolded_options',
            'style' => 'width: 420px;',
            'class' => 'validate-zero-or-greater' 
        ));
        
        $fieldset->addField('is_collapsed', 'select', array(
            'name' => 'is_collapsed',
            'label' => Mage::helper('advancedattributes')->__('Collapsed'),
            'values' => $yesnoSource,
        ), 'apply_to');
        
        $fieldset->addField('tool_tip', 'text', array(
            'label' => Mage::helper('advancedattributes')->__('Tooltip'),
            'title' => Mage::helper('advancedattributes')->__('Tooltip'),
            'name' => 'tool_tip',
            'style' => 'width: 420px;',
        ));
        
        $fieldset = $form->addFieldset('additional_form', array('legend' => Mage::helper('advancedattributes')->__('Additional Blocks')));
        
        $fieldset->addField('show_on_list', 'select', array(
            'name' => 'show_on_list',
            'label' => Mage::helper('advancedattributes')->__('Show option description and image above product listing'),
            'values' => $yesnoSource,
        ), 'apply_to');
        
        $fieldset->addField('show_on_product', 'select', array(
            'name' => 'show_on_product',
            'label' => Mage::helper('advancedattributes')->__('Show on Product'),
            'note'      => Mage::helper('advancedattributes')->__('Show options images block on product page'),
            'values' => $yesnoSource,
        ), 'apply_to');
        
        $fieldset = $form->addFieldset('seo_form', array('legend' => Mage::helper('advancedattributes')->__('Search Engine Optimization')));
        
        $fieldset->addField('no_follow_tag', 'select', array(
            'name' => 'no_follow_tag',
            'label' => Mage::helper('advancedattributes')->__('Robots NoFollow Tag'),
            'values' => $yesnoSource,
        ), 'apply_to');
        
        $fieldset->addField('no_index_tag', 'select', array(
            'name' => 'no_index_tag',
            'label' => Mage::helper('advancedattributes')->__('Robots NoIndex Tag'),
            'values' => $yesnoSource,
        ), 'apply_to');
        
        $fieldset->addField('rel_no_follow', 'select', array(
            'name' => 'rel_no_follow',
            'label' => Mage::helper('advancedattributes')->__('Rel NoFollow'),
            'note'      => Mage::helper('advancedattributes')->__('For the links in the left navigation'),
            'values' => $yesnoSource,
        ), 'apply_to');

        $fieldset = $form->addFieldset('misc_form', array('legend' => Mage::helper('advancedattributes')->__('Misc Options')));
        
        $fieldset->addField('exempt_categories', 'text', array(
            'label' => Mage::helper('advancedattributes')->__('Exclude From These Categories'),
            'title' => Mage::helper('advancedattributes')->__('Exclude From These Categories'),
            'note' => Mage::helper('advancedattributes')->__('Comma separated list of category IDs (17, 52, 23)'),
            'name' => 'exempt_categories',
            'style' => 'width: 420px;',
        ));
        
        $fieldset->addField('single_choice', 'select', array(
            'name' => 'single_choice',
            'label' => Mage::helper('advancedattributes')->__('Single Choice Only'),
            'note'      => Mage::helper('advancedattributes')->__('Disables multiple selection'),
            'values' => $yesnoSource,
        ), 'apply_to');
        
        $fieldset->addField('dependant_options', 'text', array(
            'label' => Mage::helper('advancedattributes')->__('Dependant on these options'),
            'title' => Mage::helper('advancedattributes')->__('Dependant on these options'),
            'note' => Mage::helper('advancedattributes')->__('Comma separated list of option IDs (17, 52, 23)'),
            'name' => 'dependant_options',
            'style' => 'width: 420px;',
        ));

        if ( Mage::registry('advancedattributes_data') ) {
            $form->setValues(Mage::registry('advancedattributes_data')->getData());
        }
        
        return parent::_prepareForm();
    }

}
