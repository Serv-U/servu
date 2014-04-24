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

class SD_Acm_Block_Adminhtml_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('historyGrid');
        $this->setDefaultSort('mailed_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
	
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sd_acm/emails')->getCollection();
        $collection->retrieveSentCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        //change this name to get the grid working again
        $this->addColumn('id', array(
            'header'    => Mage::helper('sd_acm')->__('ID'),
            'align'     =>'right',
            'type'      => 'number',
            'width'     => '20px',
            'index'     => 'id',
            'filter_index' => 'id',
        ));

        $this->addColumn('mailed_at', array(
            'header'    => Mage::helper('sd_acm')->__('Date Mailed'),
            'type'      => 'datetime',
            'align'     =>'left',
            'index'     => 'mailed_at',
            'width'     => '100px'
        ));
        
        $this->addColumn('recovered_date', array(
            'header'    => Mage::helper('sd_acm')->__('Recovered On'),
            'type'      => 'datetime',
            'align'     => 'left',
            'index'     => 'recovered_date',
            'default'   =>  ' ---- '
        ));

        $this->addColumn('email_number', array(
            'header'    => Mage::helper('sd_acm')->__('Email Number'),
            'type'      => 'number',
            'align'     => 'right',
            'index'     => 'email_number',
            'default'   =>  ' ---- '
        ));

        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('sd_acm')->__('Customer Email'),
            'align'     => 'left',
            'index'     => 'customer_email',
        ));
        
        $this->addColumn('customer_firstname', array(
            'header'    => Mage::helper('sd_acm')->__('First Name'),
            'align'     => 'left',
            'index'     => 'customer_firstname',
        ));
        
        $this->addColumn('customer_lastname', array(
            'header'    => Mage::helper('sd_acm')->__('Last Name'),
            'align'     => 'left',
            'index'     => 'customer_lastname',
        ));

        return parent::_prepareColumns();
    }

}
?>
