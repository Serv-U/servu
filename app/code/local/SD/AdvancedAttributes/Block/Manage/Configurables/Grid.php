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

class SD_AdvancedAttributes_Block_Manage_Configurables_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('confgurableGrid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
	
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('advancedattributes/configurables')->getCollection();
        $collection->addNoAttributeIdFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        //change this name to get the grid working again
        $this->addColumn('attribute_id', array(
            'header'    => Mage::helper('advancedattributes')->__('ID'),
            'align'     =>'right',
            'type'      => 'number',
            'width'     => '50px',
            'index'     => 'attribute_id',
            'filter_index' => 'main_table.attribute_id',
        ));

        $this->addColumn('frontend_label', array(
            'header'    => Mage::helper('advancedattributes')->__('Label'),
            'align'     =>'left',
            'index'     => 'frontend_label',
        ));

        $this->addColumn('attribute_code', array(
            'header'    => Mage::helper('advancedattributes')->__('Code'),
            'align'     => 'left',
            'index'     => 'attribute_code',
            'filter_index' => 'main_table.attribute_code',
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('advancedattributes')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getAttributeId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('advancedattributes')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'attribute_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        if ($page_id = $row->getData('id')) {
            return $this->getUrl('*/*/edit', array('id' => $page_id, 'attribute_id' => $row->getData('attribute_id')));
        }
        return $this->getUrl('*/*/edit', array('attribute_id' => $row->getAttributeId(), 'attribute_code' => $row->getData('attribute_code')));
    }

}
?>
