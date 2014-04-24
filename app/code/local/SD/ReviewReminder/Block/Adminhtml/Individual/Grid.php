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

class SD_ReviewReminder_Block_Adminhtml_Individual_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('individualGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
	
    protected function _prepareCollection() {
        $collection = Mage::getModel('reviewreminder/reviewReminder')->getCollection();
        $collection->mergeWithOrder();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() { 
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode(); 

        $this->addColumn('id', array(
            'header'    => Mage::helper('reviewreminder')->__('ID'),
            'align'     =>'right',
            'type'      => 'number',
            'width'     => '20px',
            'index'     => 'id',
            'filter_index' => 'main_table.id',
        ));
        
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('reviewreminder')->__('Order ID'),
            'align'     =>'right',
            'type'      => 'number',
            'width'     => '20px',
            'index'     => 'increment_id',
            'filter_index' => 'sales_order.increment_id',
            'default'   =>  ' ---- '
        ));
        
        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('reviewreminder')->__('Customer Email'),
            'align'     => 'left',
            'index'     => 'customer_email',
            'filter_index' => 'sales_order.customer_email',
        ));
        
        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('reviewreminder')->__('Name'),
            'align'     => 'left',
            'index'     => 'customer_name',
            'filter_index'     => 'customer_name',
            'default'   =>  ' ---- '
        ));
        
        $this->addColumn('ordered_date', array(
            'header'    => Mage::helper('reviewreminder')->__('Order Date'),
            'type'      => 'datetime',
            'width'     => '300px',
            'index'     => 'ordered_date',
            //'filter_index'     => 'main_table.abandoned_date'
            'default'   =>  ' ---- '
        ));
        
        $this->addColumn('recovered_on', array(
            'header'    => Mage::helper('reviewreminder')->__('Recovered Date'),
            'type'      => 'datetime',
            'width'     => '300px',
            'index'     => 'recovered_on',
            //'filter_index'     => 'main_table.abandoned_date'
            'default'   =>  ' ---- '
        ));
        
        $this->addColumn('email_status', array(
            'header'    => Mage::helper('reviewreminder')->__('Reminded'),
            'type'      => 'options',
            'index' => 'email_status',
            'filter_index' => 'main_table.email_status',
            'options'   => array(
                0 => Mage::helper('sd_acm')->__('Reminded 0 Times'),
                1 => Mage::helper('sd_acm')->__('Reminded 1 Time'),
                2 => Mage::helper('sd_acm')->__('Reminded 2 Times'),
                200 => Mage::helper('sd_acm')->__('Review Submitted'),
                201 => Mage::helper('sd_acm')->__('Coupon Sent'),
                205 => Mage::helper('sd_acm')->__('Coupon Used'),
            )
        ));
        
        $this->addColumn('order_status', array(
            'header'    => Mage::helper('reviewreminder')->__('Order Status'),
            'type'      => 'options',
            'index' => 'status',
            'filter_index' => 'sales_order.status',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses()
        ));
        
        $this->addColumn('coupon_order_id', array(
            'header'    => Mage::helper('reviewreminder')->__('Order Id Coupon Used On'),
            'align'     => 'right',
            'index'     => 'coupon_order_id',
            'filter_index'     => 'coupon_order_id',
            'default'   =>  ' ---- '
        ));

        return parent::_prepareColumns();
    }

}
?>
