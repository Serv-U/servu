<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manage
 *
 * @author dustinmiller
 */

class SD_Acm_Block_Adminhtml_Individual_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('individualGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
	
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sd_acm/acm')->getCollection();
        $collection->individualMerge();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode(); 
        
        $this->addColumn('id', array(
            'header'    => Mage::helper('sd_acm')->__('ID'),
            'align'     =>'right',
            'type'      => 'number',
            'width'     => '20px',
            'index'     => 'id'
        ));
        
        $this->addColumn('yesnoContacted', array(
            'header'    => Mage::helper('sd_acm')->__('Contacted?'),
            'type'      => 'options',
            'index' => 'yesnoContacted',
            'filter_index' => 'yesnoContacted',
            'options'   => array(
                null,0 => Mage::helper('sd_acm')->__('No'),
                1 => Mage::helper('sd_acm')->__('Yes'),
            )
        ));
        
        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('sd_acm')->__('Customer Email'),
            'align'     => 'left',
            'index'     => 'customer_email',
            'filter_index' => 'sq.customer_email',
        ));
        
        $this->addColumn('customer_firstname', array(
            'header'    => Mage::helper('sd_acm')->__('First Name'),
            'align'     => 'left',
            'index'     => 'customer_firstname',
            'filter_index' => 'sq.customer_firstname',
        ));
        
        $this->addColumn('customer_lastname', array(
            'header'    => Mage::helper('sd_acm')->__('Last Name'),
            'align'     => 'left',
            'index'     => 'customer_lastname',
            'filter_index' => 'sq.customer_lastname',
        ));
        
        $this->addColumn('abandoned_date', array(
            'header'    => Mage::helper('sd_acm')->__('Abandoned Date'),
            'type'      => 'datetime',
            'width'     => '300px',
            'index'     => 'abandoned_date'
            //'filter_index'     => 'main_table.abandoned_date'
        ));
        
        $this->addColumn('recovered_date', array(
            'header'    => Mage::helper('sd_acm')->__('Recovered Date'),
            'type'      => 'datetime',
            'width'     => '300px',
            'index'     => 'recovered_date'
            //'filter_index'     => 'main_table.abandoned_date'
        ));
        
        $this->addColumn('initial_cart_amount', array(
            'header'    => Mage::helper('sd_acm')->__('Cart Amount'),
            'type'      => 'currency',
            'width'     => '200px',
            'currency_code' => $currency,
            'index'     => 'initial_cart_amount',
            'filter_index'     => 'main_table.initial_cart_amount'
            
        ));
        
        $this->addColumn('base_subtotal', array(
            'header'    => Mage::helper('sd_acm')->__('Order Amount'),
            'type'      => 'currency',
            'width'     => '200px',
            'currency_code' => $currency,
            'index'     => 'base_subtotal',
            'filter_index'     => 'so.base_subtotal'
        ));
        
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sd_acm')->__('Order Date'),
            'type'      => 'datetime',
            'width'     => '300px',
            'index'     => 'created_at',
            'filter_index'     => 'so.created_at'
        ));
        
        $this->addColumn('is_active', array(
            'header'    => Mage::helper('sd_acm')->__('Active?'),
            'type'      => 'options',
            'index' => 'is_active',
            'filter_index' => 'is_active',
            'options'   => array(
                0 => Mage::helper('sd_acm')->__('No'),
                1 => Mage::helper('sd_acm')->__('Yes'),
            )
        ));
        
        $this->addColumn('is_active', array(
            'header'    => Mage::helper('sd_acm')->__('Active?'),
            'type'      => 'options',
            'index' => 'is_active',
            'filter_index' => 'is_active',
            'options'   => array(
                0 => Mage::helper('sd_acm')->__('No'),
                1 => Mage::helper('sd_acm')->__('Yes'),
            )
        ));
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('sd_acm')->__('Reminded'),
            'type'      => 'options',
            'index' => 'status',
            'filter_index' => 'main_table.status',
            'options'   => array(
                0 => Mage::helper('sd_acm')->__('Reminded 0 Times'),
                1 => Mage::helper('sd_acm')->__('Reminded 1 Time'),
                2 => Mage::helper('sd_acm')->__('Reminded 2 Times'),
                3 => Mage::helper('sd_acm')->__('Reminded 3 Times'),
            )
        ));
        
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('acm');   
       
        $this->getMassactionBlock()->addItem('activate', array(
             'label'=> Mage::helper('sd_acm')->__('Activate'),
             'url'  => $this->getUrl('*/*/massActivate'),
             'confirm' => Mage::helper('sd_acm')->__('Are you sure?')
        ));
        
        $this->getMassactionBlock()->addItem('deactivate', array(
             'label'=> Mage::helper('sd_acm')->__('Deactivate'),
             'url'  => $this->getUrl('*/*/massDeactivate'),
             'confirm' => Mage::helper('sd_acm')->__('Are you sure?')
        ));
        
        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getData('id')));
    }
}
?>
