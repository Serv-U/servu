<?php

class SD_AdvancedAttributes_Block_Manage_Configurables_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $model = Mage::registry('advancedattributes_data');
        
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/manage_configurables/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('display_form', array('legend' => Mage::helper('advancedattributes')->__('Option Images')));
        
        $fieldset->addfield('id', 'hidden', array('name' => 'id', 'value' => $this->getRequest()->getParam('id')));
        $fieldset->addfield('attribute_id', 'hidden', array('name' => 'attribute_id', 'value' => $this->getRequest()->getParam('attribute_id')));
        
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $model->getAttributeCode());
        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
            
            foreach($options as $option) {
                $optionModel = Mage::getSingleton('advancedattributes/configurableOptions')->loadFromOptionId($option['value']);
                
                if($optionModel->getId()) {
                    $fieldset->addField('table_id_'.$optionModel->getId(), 'hidden', array('name' => 'table_id_'.$optionModel->getId(), 'value' => $optionModel->getId()));
                }
                
                $fieldset->addField('option_id_'.$option['value'], 'hidden', array('name' => 'option_id_'.$option['value'], 'value' => $option['value']));
                $fieldset->addField('option_label_'.$option['label'], 'hidden', array('name' => 'option_label_'.$option['label'], 'value' => $option['label']));
                
                $fieldset->addField('product_view_image_'.$option['value'], 'image', array(
                    'name'      => 'product_view_image_'.$option['value'],
                    'label'     => Mage::helper('adminhtml')->__($option['label']),
                    'title'     => Mage::helper('adminhtml')->__($option['label']),
                    'required'  => false,
                ));

            }
        }
        
        $values = $model->getData();

        foreach($options as $option) {
            $optionModel = Mage::getSingleton('advancedattributes/configurableOptions')->loadFromOptionId($option['value']);
            if($optionModel->getProductViewImage() != '') {
                $values['product_view_image_'.$option['value']] = 'catalog/attributes/configurables/view/'.$optionModel->getProductViewImage();  
            }
        } 
        
        $form->setValues($values);
        
        return parent::_prepareForm();
    }

}
