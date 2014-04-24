<?php

class Aero_Catalogrequest_Block_Adminhtml_Catalogrequest_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('catalogrequest_form', array('legend'=>Mage::helper('catalogrequest')->__('Item information')));
     
      $fieldset->addField('fname', 'text', array(
          'label'     => Mage::helper('catalogrequest')->__('First Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'fname',
      ));

      $fieldset->addField('lname', 'text', array(
          'label'     => Mage::helper('catalogrequest')->__('Last Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'lname',
      ));
      
      $fieldset->addField('company', 'text', array(
          'label'     => Mage::helper('catalogrequest')->__('Company'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'company',
      ));

      $fieldset->addField('address1', 'text', array(
          'label'     => Mage::helper('catalogrequest')->__('Address'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'address1',
      ));

      $fieldset->addField('address2', 'text', array(
          'name'      => 'address2',
      ));

      $fieldset->addField('city', 'text', array(
          'label'     => Mage::helper('catalogrequest')->__('City'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'city',
      ));

      $fieldset->addField('state', 'text', array(
          'label'     => Mage::helper('catalogrequest')->__('State'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'state',
      ));

      $fieldset->addField('zip', 'text', array(
          'label'     => Mage::helper('catalogrequest')->__('Zip'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'zip',
      ));

      $fieldset->addField('country', 'text', array(
          'label'     => Mage::helper('catalogrequest')->__('Country'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'country',
      ));

      $fieldset->addField('phone', 'text', array(
          'label'     => Mage::helper('catalogrequest')->__('Phone'),
          'name'      => 'phone',
      ));

      $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('catalogrequest')->__('Email'),
          'name'      => 'email',
      ));

      $fieldset->addField('product_interest', 'select', array(
          'label'     => Mage::helper('catalogrequest')->__('Product Interest'),
          'name'      => 'product_interest',
          'values'    => array(
              array(
                  'value'     => 'Bar Equipment',
                  'label'     => Mage::helper('catalogrequest')->__('Bar Equipment'),
              ),
              array(
                  'value'     => 'Glassware',
                  'label'     => Mage::helper('catalogrequest')->__('Glassware'),
              ),
              array(
                  'value'     => 'Furniture',
                  'label'     => Mage::helper('catalogrequest')->__('Furniture'),
              ),
              array(
                  'value'     => 'Restaurant Equipment',
                  'label'     => Mage::helper('catalogrequest')->__('Restaurant Equipment'),
              ),
              array(
                  'value'     => 'Food Prep',
                  'label'     => Mage::helper('catalogrequest')->__('Food Prep'),
              ),
              array(
                  'value'     => 'Pizza Equipment',
                  'label'     => Mage::helper('catalogrequest')->__('Pizza Equipment'),
              ),
              array(
                  'value'     => 'Refrigeration',
                  'label'     => Mage::helper('catalogrequest')->__('Refrigeration'),
              ),
          ),
      ));

      $fieldset->addField('heardofus', 'select', array(
          'label'     => Mage::helper('catalogrequest')->__('Heard From'),
          'name'      => 'product_interest',
          'values'    => array(
              array(
                  'value'     => '',
                  'label'     => Mage::helper('catalogrequest')->__('Not Answered'),
              ),
              array(
                  'value'     => 'ISFRD',
                  'label'     => Mage::helper('catalogrequest')->__('Friend'),
              ),
              array(
                  'value'     => 'ISCAT',
                  'label'     => Mage::helper('catalogrequest')->__('Catalog'),
              ),
              array(
                  'value'     => 'ISFLY',
                  'label'     => Mage::helper('catalogrequest')->__('Serv-U Sales Flyer'),
              ),
              array(
                  'value'     => 'ISEML',
                  'label'     => Mage::helper('catalogrequest')->__('Email Promotion'),
              ),
              array(
                  'value'     => 'ISSOC',
                  'label'     => Mage::helper('catalogrequest')->__('Social Promotion'),
              ),
              array(
                  'value'     => 'ISREP',
                  'label'     => Mage::helper('catalogrequest')->__('Sales Rep'),
              ),
              array(
                  'value'     => 'ISSEG',
                  'label'     => Mage::helper('catalogrequest')->__('Search Engine - Google'),
              ),
              array(
                  'value'     => 'ISSEY',
                  'label'     => Mage::helper('catalogrequest')->__('Search Engine - Yahoo'),
              ),
              array(
                  'value'     => 'ISSEM',
                  'label'     => Mage::helper('catalogrequest')->__('Search Engine - Bing'),
              ),
              array(
                  'value'     => 'ISSEN',
                  'label'     => Mage::helper('catalogrequest')->__('Search Engine - Netscape'),
              ),
              array(
                  'value'     => 'ISSEA',
                  'label'     => Mage::helper('catalogrequest')->__('Search Engine - AOL'),
              ),
              array(
                  'value'     => 'ISSEO',
                  'label'     => Mage::helper('catalogrequest')->__('Search Engine - Other'),
              ),
          ),
      ));

      $fieldset->addField('res_bus', 'select', array(
          'label'     => Mage::helper('catalogrequest')->__('Business/Residential'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 'residential',
                  'label'     => Mage::helper('catalogrequest')->__('Residential'),
              ),

              array(
                  'value'     => 'business',
                  'label'     => Mage::helper('catalogrequest')->__('Business'),
              ),
          ),
      ));
      
      $fieldset->addField('exist_customer', 'select', array(
          'label'     => Mage::helper('catalogrequest')->__('Existing Customer'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('catalogrequest')->__('No'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('catalogrequest')->__('Yes'),
              ),
          ),
      ));
      
      $fieldset->addField('first_catalog', 'select', array(
          'label'     => Mage::helper('catalogrequest')->__('First catalog'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('catalogrequest')->__('No'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('catalogrequest')->__('Yes'),
              ),
          ),
      ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('catalogrequest')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('catalogrequest')->__('Pending'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('catalogrequest')->__('Processed'),
              ),
          ),
      ));


     
      if ( Mage::getSingleton('adminhtml/session')->getCatalogrequestData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCatalogrequestData());
          Mage::getSingleton('adminhtml/session')->setCatalogrequestData(null);
      } elseif ( Mage::registry('catalogrequest_data') ) {
          $form->setValues(Mage::registry('catalogrequest_data')->getData());
      }
      return parent::_prepareForm();
  }
}