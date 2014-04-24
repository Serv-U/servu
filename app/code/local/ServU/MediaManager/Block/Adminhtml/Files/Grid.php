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

class ServU_MediaManager_Block_Adminhtml_Files_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('filesGrid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mediamanager/mediamanager')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('mediamanager')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'id',
        ));

        $this->addColumn('file_title', array(
            'header'    => Mage::helper('mediamanager')->__('Public Title'),
            'align'     =>'left',
            'index'     => 'file_title',
        ));

        $this->addColumn('file_name', array(
            'header'    => Mage::helper('mediamanager')->__('File Name'),
            'align'     => 'left',
            'index'     => 'file_name',
        ));

        $this->addColumn('file_extension', array(
            'header'    => Mage::helper('mediamanager')->__('Type'),
            'align'     => 'left',
            'index'     => 'file_extension',
        ));

//        $this->addColumn('file_size', array(
//            'header'    => Mage::helper('mediamanager')->__('Size'),
//            'align'     => 'left',
//            'index'     => 'file_size',
//        ));

        $this->addColumn('formatted_file_size', array(
            'header'    => Mage::helper('mediamanager')->__('Size'),
            'align'     => 'left',
            'index'     => 'file_size',
            'type'      => 'action',
            'width'     => '150px',
            'renderer'  => new ServU_MediaManager_Block_Adminhtml_Renderer_Size(),
        ));


        $this->addColumn('no_follow', array(
            'header'    => Mage::helper('mediamanager')->__('No Follow'),
            'align'     => 'left',
            'index'     => 'no_follow',
            'type'      => 'options',
            'options'   => array(
                1 => 'Enabled',
                0 => 'Disabled',
            ),
        ));

        $this->addColumn('file_status', array(
                'header'    => Mage::helper('mediamanager')->__('Status'),
                'align'     => 'left',
                'index'     => 'file_status',
                'type'      => 'options',
                'options'   => array(
                    1 => 'Enabled',
                    0 => 'Disabled',
                ),
        ));
                
        $this->addColumn('file_manufacturer_date', array(
            'header'    => Mage::helper('mediamanager')->__('Manufacturer Date'),
            'align'     => 'left',
            'index'     => 'file_manufacturer_date',
        ));
        
        $this->addColumn('date_modified', array(
            'header'    => Mage::helper('mediamanager')->__('Date Modified'),
            'align'     => 'left',
            'index'     => 'date_modified',
        ));
        
        
        $this->addColumn('date_created', array(
            'header'    => Mage::helper('mediamanager')->__('Date Created'),
            'align'     => 'left',
            'index'     => 'date_created',
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('mediamanager')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mediamanager')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('mediamanager')->__('View File'),
                        'url'       => array('base'=> '*/*/view'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
        ));

//20131007 DEBUGGING - Ensure that file exists in directory (Used for first mass upload)
//        $this->addColumn('file_exists', array(
//            'header'    => Mage::helper('mediamanager')->__('File Exists'),
//            'align'     => 'center',
//            'renderer'  => 'mediamanager/adminhtml_widget_grid_column_renderer_fileexists',
//            'filter'    => false,
//            'sortable'    => false,
//        ));
        
        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('mediamanager');
        
        $this->getMassactionBlock()->addItem('enable', array(
                'label' => Mage::helper('catalog')->__('Enable'),
                'url'   => $this->getUrl('*/*/massEnable', array('_current'=>true)),
        ));

        $this->getMassactionBlock()->addItem('disable', array(
                'label' => Mage::helper('catalog')->__('Disable'),
                'url'   => $this->getUrl('*/*/massDisable', array('_current'=>true)),
        ));

        $this->getMassactionBlock()->addItem('delete', array(
                'label' => Mage::helper('catalog')->__('Delete'),
                'url'   => $this->getUrl('*/*/massDelete', array('_current'=>true)),
        ));

        return $this;
    }    

    public function getRowUrl($row)
    {
        if ($page_id = $row->getData('id')) {
            return $this->getUrl('*/*/edit', array('id' => $page_id, 'file_id' => $page_id));
        }
        return $this->getUrl('*/*/edit', array('id' => $row->getFileId(), 'file_id' => $row->getFileId()));
    }
}
?>
