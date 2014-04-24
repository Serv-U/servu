<?php
/**
 * Description of Products
 * @author andrewprendergast
 */

class ServU_BlogProducts_Block_Adminhtml_Blog_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct() {
        parent::__construct();
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

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('catalog/product_collection')
            //Filter to only include 'Catalog, Search' products
            ->addAttributeToFilter('visibility', array('eq' => 4))
            ->addAttributeToSelect('name');

        $tm_id = $this->getRequest()->getParam('id');
        if(!isset($tm_id)) {
            $tm_id = 0;
        }

        //Mage::getResourceModel('blogproducts/blogproducts')->addGridPosition($collection,$tm_id);
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column) {
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
    
    protected function _prepareColumns() {
        $this->addColumn('in_products', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'product_id',
                'values'            => $this->_getSelectedProducts(),
                'align'             => 'center',
                'index'             => 'entity_id',
        ));
        
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('blogproducts')->__('ID'),
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('blogproducts')->__('Name'),
            'index'     => 'name'
        ));
        
        $this->addColumn('sku', array(
            'header' => Mage::helper('blogproducts')->__('sku'),
            'index' => 'sku',
        ));
        
        $this->addColumn('type', array(
            'header'    => Mage::helper('blogproducts')->__('Type'),
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
            'header'    => Mage::helper('blogproducts')->__('Attrib. Set Name'),
            'width'     => 130,
            'index'     => 'attribute_set_id',
            'type'      => 'options',
            'options'   => $sets,
        ));
        
        /*
        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Product Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));
        */
        
        $this->addColumn('position', array(
            'header'            => Mage::helper('blogproducts')->__('Serializer ID'),
            'name'              => 'position',
            'type'              => 'number',
            'width'             => 80,
            'index'             => 'position',
            'editable'          => true,
            'edit_only'         => true,
            'sortable'          => false,
            'filter'            => false,
        ));

        return parent::_prepareColumns();
    }

    protected function _getSelectedProducts() {
        $products = array_keys($this->getSelectedProducts());
        return $products;
    }
    
    public function getGridUrl() {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/productsgrid', array('_current'=>true));
    }
    
    public function getSelectedProducts() {
        $tm_id = $this->getRequest()->getParam('id');
        if(!isset($tm_id)) {
            $tm_id = 0;
        }
        
        $products = Mage::getModel('blogproducts/blogproducts')
                ->getCollection()
                ->addFieldToFilter('blog_id', $tm_id);
        
        $ProductsArray = array();
        foreach ($products as $prod){
            $ProductsArray[$prod->product_id] = array('position'=>$prod->position);
        }

        return $ProductsArray;
    }
    
    public function getRowUrl($row) {
        //This removes the Hash link from grid items (if not removed page will jump back to top whenever user clicks on an item)
        return null;
    }
}