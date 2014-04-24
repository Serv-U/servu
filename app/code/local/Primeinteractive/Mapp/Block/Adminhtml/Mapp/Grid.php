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

class Primeinteractive_Mapp_Block_Adminhtml_Mapp_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct() {
      parent::__construct();
      $this->setId('mappGrid');
      $this->setDefaultSort('mapp_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection() {
      $collection = Mage::getModel('mapp/mapp')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns() {
    $this->addColumn('mapp_id', array(
        'header'    => Mage::helper('mapp')->__('ID'),
        'align'     =>'right',
        'width'     => '50px',
        'index'     => 'mapp_id',
    ));

    $this->addColumn('name', array(
        'header'    => Mage::helper('mapp')->__('Name'),
        'align'     =>'left',
        'index'     => 'name',
    ));

    $this->addColumn('emailid', array(
        'header'    => Mage::helper('mapp')->__('Email Id'),
        'align'     =>'left',
        'index'     => 'emailid',
    ));

    $this->addColumn('productname', array(
        'header'    => Mage::helper('mapp')->__('Product Name'),
        'align'     =>'left',
        'index'     => 'productname',
    ));

    $this->addColumn('producturl', array(
        'header'    => Mage::helper('mapp')->__('Product Url'),
        'align'     =>'left',
        'index'     => 'producturl',
    ));

    $this->addColumn('sku', array(
        'header'    => Mage::helper('mapp')->__('Sku'),
        'align'     =>'left',
        'index'     => 'sku',
    ));

    $this->addColumn('mapprice', array(
        'header'    => Mage::helper('mapp')->__('Mapp Price'),
        'align'     =>'left',
        'type'  => 'price',
        'currency_code' => Mage::app()->getStore(0)->getBaseCurrency()->getCode(),
        'index'     => 'mapprice',
    ));

//    $this->addColumn('telephone', array(
//        'header'    => Mage::helper('mapp')->__('Telephone'),
//        'align'     =>'left',
//        'index'     => 'telephone',
//    ));

    $this->addColumn('coupon_code', array(
        'header'    => Mage::helper('mapp')->__('Coupon Code'),
        'align'     =>'left',
        'index'     => 'coupon_code',
    ));
    
    $this->addColumn('created_time', array(
        'header'    => Mage::helper('mapp')->__('Date Created'),
        'align'     => 'left',
        'index'     => 'created_time',
        'type'      => 'datetime',
        'format'    => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG),
    ));
    
    $this->addColumn('update_time', array(
        'header'    => Mage::helper('mapp')->__('Date Updated'),
        'align'     => 'left',
        'index'     => 'update_time',
        'type'      => 'datetime',
        'format'    => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG),
    ));
    
    $this->addColumn('store_id', array(
        'header'    => Mage::helper('mapp')->__('Store ID'),
        'align'     => 'left',
        'index'     => 'store_id',
        'type'      => 'store', 
    ));    

     /* $this->addColumn('status', array(
          'header'    => Mage::helper('mapp')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));*/

    $this->addColumn('action',
        array(
            'header'    =>  Mage::helper('mapp')->__('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('mapp')->__('Edit'),
                    'url'       => array('base'=> '*/*/edit'),
                    'field'     => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));

        //$this->addExportType('*/*/exportCsv', Mage::helper('mapp')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('mapp')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('mapp_id');
        $this->getMassactionBlock()->setFormFieldName('mapp');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('mapp')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('mapp')->__('Are you sure?')
        ));

       /* $statuses = Mage::getSingleton('mapp/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('mapp')->__('Change status'),
             'url'  => $this->getUrl('*//*massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('mapp')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;*/
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}