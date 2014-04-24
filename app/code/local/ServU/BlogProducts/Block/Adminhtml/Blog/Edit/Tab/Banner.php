<?php

class ServU_BlogProducts_Block_Adminhtml_Blog_Edit_Tab_Banner extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('blog_banner_form', array('legend' => Mage::helper('blogproducts')->__('Banner HTML')));

        $fieldset->addField('banner', 'editor', array(
            'name' => 'banner',
            'value' => Mage::getModel('blogproducts/blogbanners')->getBanner($this->getRequest()->getParam('id')),
            'label' => Mage::helper('blogproducts')->__('Banner Image/HTML'),
            'style' => 'width: 450px;',
        ));

        return parent::_prepareForm();
    }

}
