<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * DISCLAIMER
 *
 *
 * @category   Primeinteractive
 * @package    Primeinteractive_Mapp
 * @version    1.0
 * @copyright   Copyright (c) 2012 Prime Interactive, Inc.
 */


class Primeinteractive_Mapp_Block_Adminhtml_Mapp_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('mapp_form', array('legend'=>Mage::helper('mapp')->__('Item information')));

        $fieldset->addField('store_id', 'select', array(
            'label'   => Mage::helper('mapp')->__('Requested from Store'),
            'name'    => 'store_id',
            'values'  => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
        ));
        
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('mapp')->__('Customer Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));

        $fieldset->addField('emailid', 'text', array(
            'label'     => Mage::helper('mapp')->__('Customer Email'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'emailid',
        ));

        $fieldset->addField('productname', 'text', array(
            'label'     => Mage::helper('mapp')->__('Product Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'productname',
        ));

        $fieldset->addField('producturl', 'text', array(
            'label'     => Mage::helper('mapp')->__('Product Url'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'producturl',
        ));

        $fieldset->addField('sku', 'text', array(
            'label'     => Mage::helper('mapp')->__('Sku'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'sku',
        ));

        $fieldset->addField('telephone', 'text', array(
            'label'     => Mage::helper('mapp')->__('Telephone'),
            'name'      => 'telephone',
        ));

        $fieldset->addField('coupon_code', 'text', array(
            'label'     => Mage::helper('mapp')->__('Coupon Code'),
            'name'      => 'coupon_code',
            'readonly'  => true,
            'after_element_html' => '<span class="notice">Field is autopopulated the first time mapp is saved.</span>',
        ));

        $fieldset->addField('mapprice', 'text', array(
            'label'     => Mage::helper('mapp')->__('Mapp Price'),
            'name'      => 'mapprice',
            'readonly'  => true,
            'after_element_html' => '<span class="notice">Field is autopopulated the first time mapp is saved.</span>',
        ));
        
        $fieldset->addField('created_time', 'date', array(
            'label'     => Mage::helper('mapp')->__('Date/Time Created'),
            'name'      => 'created_time',
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'value'     => '1/18/14 09:43 PM',
            'time'      => true,
            'after_element_html' => '<br/><span class="notice">Date/Time value is UTC (CST +6).<br/>Field is autopopulated the first time mapp is saved.<br/> To see how long coupons are valid after their creation date, see System->Configuration->MAPP</span>',
        ));
        
        /*
        $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('mapp')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mapp')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mapp')->__('Disabled'),
              ),
          ),
        ));
        */

        if ( Mage::getSingleton('adminhtml/session')->getMappData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getMappData());
            Mage::getSingleton('adminhtml/session')->setMappData(null);
        } elseif ( Mage::registry('mapp_data') ) {
            $form->setValues(Mage::registry('mapp_data')->getData());
        }

        //If this is a new request, set default store to Serv-U
        $id = $this->getRequest()->getParam('id');
        if(empty($id)){
            $default = array('store_id' => 2);
            $form->setValues($default);
        }

        return parent::_prepareForm();
    }
}