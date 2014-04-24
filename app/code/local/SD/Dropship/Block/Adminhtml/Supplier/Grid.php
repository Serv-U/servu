<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Grid
 *
 * @author dustinmiller
 */
class SD_Dropship_Block_Adminhtml_Supplier_Grid 
    extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('supplierGrid');
        $this->setDefaultSort('attribute');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sd_dropship/supplier')->getCollection();
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $res = Mage::getResourceModel('sd_dropship/supplier');

        $this->addColumn('value', array(
            'header'    => Mage::helper('sd_dropship')->__('Supplier Name'),
            'align'     => 'left',
            'index'     => 'value'
        ));

         if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('sd_dropship')->__('Stores'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

        return parent::_prepareColumns();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        if ($page_id = $row->getData('id')) {
            return $this->getUrl('*/*/edit', array('id' => $page_id));
        }
        return $this->getUrl('*/*/edit', array(
        	'attribute_code' => $row->getData('attribute_code'),
        	'option_id' => $row->getData('option_id'),
        	'store_id' => $row->getData('store_id')
		));

    }
}

?>
