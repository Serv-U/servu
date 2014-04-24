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

class ServU_ClearanceManagement_Block_Adminhtml_Disable_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct()
    {   
        parent::__construct();
        $this->setId('disableGrid');
        $this->setDefaultSort('sku');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('product_filter');
    }
	
    protected function _prepareCollection() {
        $model = Mage::getModel("catalog/product"); 
        $collection = $model->getCollection();
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        
        $collection->addAttributeToFilter('clearance_item', array('eq' => '1'))
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('catalog_number');
        
        $collection
            ->joinField(
            'qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left')
            ->addAttributeToFilter('qty', array("lt" => 1));
        $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
        $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));
        
        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '150px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
        
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '200px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '200px',
                'index' => 'sku',
        ));
        
        $this->addColumn('catalog_number',
            array(
                'header'=> Mage::helper('catalog')->__('Catalog Number'),
                'index' => 'catalog_number',
                'width' => '30px',
        ));
        
        $this->addColumn('qty',
            array(
                'header'=> Mage::helper('catalog')->__('Qty'),
                'index' => 'qty',
                'type'  => 'number',
                'width' => '30px',
        ));
        
        $this->addColumn('clearance_item',
            array(
                'header'=> Mage::helper('catalog')->__('Is Clearance'),
                'index' => 'clearance_item',
                'width' => '30px',
                'type'      => 'options',
                'options'   => array(
                    0 => Mage::helper('catalog')->__('No'),
                    1 => Mage::helper('catalog')->__('Yes')
                )
        ));
        
        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '170px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/catalog_product/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('clearance');

        $this->getMassactionBlock()->addItem('disable', array(
                'label' => Mage::helper('catalog')->__('Disable Products'),
                'url'   => $this->getUrl('*/*/massDisable', array('_current'=>true)),
            ));

        return $this;
    }
    
    public function getRowUrl($row) {
        return $this->getUrl('*/catalog_product/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }

}
?>
