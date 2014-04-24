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
class SD_Manager_Block_Adminhtml_Manufacturer_Edit_Form 
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if (Mage::getVersion() > '1.4') {
            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head'))) {
                $block->setCanLoadTinyMce(true);
            }
	}
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}

?>