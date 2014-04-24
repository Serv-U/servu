<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Products
 *
 * @author dustinmiller
 */

class ServU_MediaManager_Block_Adminhtml_Files_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{
//    protected function _gridSelectAll(){
//        $html = '
//                <script type="text/javascript">alert("test");</script>
//                ';
//        
//        $html .= '<a href="#">Select All (both visible and non-visible)</a>';
//        return $html;
//    }
    
    public function __construct()
    {
        parent::__construct();
        
        //Fix Admin Grid issues and add select option to select all items 
        //echo $this->_gridSelectAll();

        $this->setId('productsGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('name');

        if (Mage::app()->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_products' => 1));
            $this->setDefaultSort('in_products');
        }
        $this->setDefaultSort('entity_id');
  
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            //Filter to only include 'Catalog, Search' products
            ->addAttributeToFilter('visibility', array('eq' => 4))
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('manufacturer');
//            ->addAttributeToSelect('*');
        //$fields = $this->getCollection()->addFieldToFilter('form_id', $form_id);
        //$manufacturer = Mage::getModel('sd_manager/manufacturer')->getCollection();
//        $collection->getSelect()->joinLeft(array('ea' => $collection->getTable('sd_manager/manufacturer')), 'ea.attribute_option_id=main_table.manufacturer');

        
//TO GET SPECIFIC MANUFACTURER NAME FROM ID
//        $manufacturer = Mage::getModel('sd_manager/manufacturer')->getCollection()
//                ->addFieldToFilter('attribute_option_id','4668')->getFirstItem();
//        Mage::log($manufacturer->getData('value'));
        
        $tm_id = $this->getRequest()->getParam('id');
        if(!isset($tm_id)) {
            $tm_id = 0;
        }

        //Mage::getResourceModel('mediamanager/products')->addGridPosition($collection,$tm_id);
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'product_id',
                'values'            => $this->_getSelectedProducts(),
                'align'             => 'center',
                'index'             => 'entity_id',
        ));
        
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('mediamanager')->__('ID'),
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('mediamanager')->__('Name'),
            'index'     => 'name'
        ));
        
        $this->addColumn('sku', array(
            'header' => Mage::helper('mediamanager')->__('sku'),
            'index' => 'sku',
        ));
        
        $this->addColumn('manufacturer', array(
            'header' => Mage::helper('mediamanager')->__('Manufacturer'),
            'index' => 'manufacturer',
            'type'      => 'options',
            'options'   => Mage::helper('mediamanager')->getManufacturerArray(),
        ));
        
        $this->addColumn('type', array(
            'header'    => Mage::helper('mediamanager')->__('Type'),
            'width'     => 100,
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header'    => Mage::helper('mediamanager')->__('Attrib. Set Name'),
            'width'     => 130,
            'index'     => 'attribute_set_id',
            'type'      => 'options',
            'options'   => $sets,
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
                        'caption'   => Mage::helper('mediamanager')->__('Edit Product'),
                        'url'       => array('base'=> 'adminhtml/catalog_product/edit/tab/product_info_tabs_attached_files'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _getSelectedProducts()
    {
        $products = array_keys($this->getSelectedProducts());
        return $products;
    }
    
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/productsgrid', array('_current'=>true));
    }
    
    public function getSelectedProducts() 
    {
        $tm_id = $this->getRequest()->getParam('id');
        if(!isset($tm_id)) {
            $tm_id = 0;
        }
        
        $products = Mage::getModel('mediamanager/products')
                ->getCollection()
                ->addFieldToFilter('file_id', $tm_id);
        
        $ProductsArray = array();
        foreach ($products as $prod){
            $ProductsArray[$prod->product_id] = array('position'=>$prod->position);
        }

        return $ProductsArray;
    }
    
    public function getRowUrl($row)
    {
        //This removes the Hash link from grid items (if not removed page will jump back to top whenever user clicks on an item)
        return null;
    }
    
//    protected function _prepareMassaction()
//    {
//        $this->setMassactionIdField('in_products');
//        $this->getMassactionBlock()->setFormFieldName('mediamanager');
//
//        $this->getMassactionBlock()->addItem('enable', array(
//                'label' => Mage::helper('catalog')->__(''),
//                'url'   => $this->getUrl('*/*', array('_current'=>true)),
//        ));
//        
////        $this->getMassactionBlock()->addItem('enable', array(
////                'label' => Mage::helper('catalog')->__('Enable'),
////                'url'   => $this->getUrl('*/*/massEnable', array('_current'=>true)),
////        ));
////
////        $this->getMassactionBlock()->addItem('disable', array(
////                'label' => Mage::helper('catalog')->__('Disable'),
////                'url'   => $this->getUrl('*/*/massDisable', array('_current'=>true)),
////        ));
////
////        $this->getMassactionBlock()->addItem('delete', array(
////                'label' => Mage::helper('catalog')->__('Delete'),
////                'url'   => $this->getUrl('*/*/massDelete', array('_current'=>true)),
////        ));
//
//        return $this;
//    }
}