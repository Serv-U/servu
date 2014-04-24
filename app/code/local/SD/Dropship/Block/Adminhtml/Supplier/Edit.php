<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Edit
 *
 * @author dustinmiller
 */
class SD_Dropship_Block_Adminhtml_Supplier_Edit 
    extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected $attributeCode = 'supplier';

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_supplier';
        $this->_blockGroup = 'sd_dropship';

        parent::__construct();

    	$this->setData('form_action_url', $this->getUrl('*/supplier/save'));
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save Information'));
        $this->_updateButton('delete', 'label', Mage::helper('adminhtml')->__('Delete Information'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry('sd_dropship_supplier')->getId()) {
                $stores = Mage::app()->getStores(true, false);
                
        	$model = Mage::registry('sd_dropship_supplier');
        	/* @var $model SD_Dropship_Model_Supplier */
        	$storeName = 'Default';
        	if(isset($stores[$model->getAttributeValueStoreId()])) {
        		$storeName = $stores[$model->getAttributeValueStoreId()]->getName();
        	}

        	$text = Mage::helper('adminhtml')->__('Edit %s Information for %s (in %s store)', $model->getFrontendLabel(), $model->getValue(), $storeName);
            return $text;
        } else {
			$stores = Mage::app()->getStores(true, false);
        	$model = Mage::registry('sd_dropship_supplier');
        	/* @var $model SD_Dropship_Model_Supplier */
        	$storeName = 'Default';
        	if($stores[$model->getStoreId()]) {
        		$storeName = $stores[$model->getStoreId()]->getName();
        	}

        	$text = Mage::helper('adminhtml')->__('New %s Information for %s (in %s store)', $model->getFrontendLabel(), $model->getValue(), $storeName);
            return $text;
        }
    }

}

?>