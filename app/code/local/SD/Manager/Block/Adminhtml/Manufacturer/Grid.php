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
class SD_Manager_Block_Adminhtml_Manufacturer_Grid 
    extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('manufacturerGrid');
        $this->setDefaultSort('attribute');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sd_manager/manufacturer')->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $res = Mage::getResourceModel('sd_manager/manufacturer');
        
        $this->addColumn('page_title', array(
            'header'    => Mage::helper('sd_manager')->__('Page Title'),
            'align'     => 'left',
            'index'     => 'page_title',
        ));

        $this->addColumn('value', array(
            'header'    => Mage::helper('sd_manager')->__('Manufacturer Name'),
            'align'     => 'left',
            'index'     => 'value'
        ));

         if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('sd_manager')->__('Stores'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }
        
        $this->addColumn('url_key', array(
            'header'    => Mage::helper('sd_manager')->__('URL Key'),
            'align'     => 'left',
            'index'     => 'url_key',
        ));

        $this->addColumn('is_enabled', array(
            'header'    => Mage::helper('sd_manager')->__('Status'),
            'index'     => 'is_enabled',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('sd_manager')->__('Disabled'),
                1 => Mage::helper('sd_manager')->__('Enabled')
            ),
        ));
        
        $this->addColumn('is_featured', array(
            'header'    => Mage::helper('sd_manager')->__('Featured'),
            'index'     => 'is_featured',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('sd_manager')->__('No'),
                1 => Mage::helper('sd_manager')->__('Yes')
            ),
        ));
        
        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('sd_manager')->__('Sort Order'),
            'align'     => 'left',
            'index'     => 'sort_order',
            'width'     => '50px',
            'type'      => 'number',
            'filter_index'=>'main_table.sort_order'
        ));
        /*$this->addColumn('attr_actions', array(
            'header'    => Mage::helper('')->__('Action'),
            'width'     => 10,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'sd_manager/adminhtml_manufacturer_grid_renderer_action',
        ));*/

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