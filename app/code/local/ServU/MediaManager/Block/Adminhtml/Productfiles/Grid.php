<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductFilesGrid
 *
 * @author dustinmiller
 */

class ServU_MediaManager_Block_Adminhtml_Productfiles_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productfilesGrid');
        $this->setUseAjax(true);

        if (Mage::app()->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_products' => 1));
            $this->setDefaultSort('in_products');
        }
        $this->setDefaultSort('id');
        //$this->setDefaultSort('file_title');

        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mediamanager/mediamanager')->getCollection();
        
        $tm_id = $this->getRequest()->getParam('id');
        if(!isset($tm_id)) {
            $tm_id = 0;
        }

        Mage::getResourceModel('mediamanager/productfiles')->addGridPosition($collection,$tm_id);
                
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productfileIds = $this->_getSelectedProductfiles();
            if (empty($productfileIds)) {
                $productfileIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', array('in'=>$productfileIds));
            } else {
                if($productfileIds) {
                    $this->getCollection()->addFieldToFilter('id', array('nin'=>$productfileIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }    
    
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'productfile',
                'values'            => $this->_getSelectedProductfiles(),
                'align'             => 'center',
                'index'             => 'id'
        ));

        $this->addColumn('id', array(
            'header'    => Mage::helper('mediamanager')->__('ID'),
            'align'     =>'left',
            'index'     => 'id',
        ));
                
        $this->addColumn('file_title', array(
            'header'    => Mage::helper('mediamanager')->__('Title'),
            'align'     =>'left',
            'index'     => 'file_title',
        ));

        $this->addColumn('file_name', array(
            'header'    => Mage::helper('mediamanager')->__('Filename'),
            'align'     => 'left',
            'index'     => 'file_name',
        ));

        $this->addColumn('file_extension', array(
            'header'    => Mage::helper('mediamanager')->__('File Extension'),
            'align'     => 'left',
            'index'     => 'file_extension',
        ));

        $this->addColumn('file_size', array(
            'header'    => Mage::helper('mediamanager')->__('File Size'),
            'align'     => 'left',
            'index'     => 'file_size',
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

        $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Position'),
            'name'              => 'position',
            'width'             => 80,
            'index'             => 'position',
            'editable'          => true,
            'edit_only'         => true,
            'sortable'          => false,
            'filter'            => false,
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
                        'url'       => array('base'=> 'mediamanager_admin/adminhtml_files/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _getSelectedProductfiles()
    {
        $productfiles = array_keys($this->getSelectedProductfiles());
        return $productfiles;
    }

    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/productfilesgrid', array('_current'=>true));
    }
    
    public function getSelectedProductfiles() 
    {
        $tm_id = $this->getRequest()->getParam('id');
        if(!isset($tm_id)) {
            $tm_id = 0;
        }
        
        $product_id = $this->getRequest()->getParam('id');
        if(!isset($product_id)) {
            $product_id = 0;
        }
                
        $selected_files = Mage::getModel('mediamanager/products')
                ->getCollection()
                ->addFieldToFilter('product_id', $product_id);

        $productfiles = array();
        $prodfileIds = array();

        foreach($selected_files as $products){
            $productfiles[] = $products->file_id;
        }

        foreach($productfiles as $productfile) {
            foreach($productfiles as $prodfile) {
                $prodfileIds[$prodfile] = array('position'=>$prodfile);
            }
        }
        return $prodfileIds;
    } 

    public function getRowUrl($row)
    {
        //This removes the Hash link from grid items (if not removed page will jump back to top whenever user clicks on an item)
        return null;
    }    
    
}
?>