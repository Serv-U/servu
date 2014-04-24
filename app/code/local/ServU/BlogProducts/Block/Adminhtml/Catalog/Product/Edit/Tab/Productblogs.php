<?php
 
class ServU_BlogProducts_Block_Adminhtml_Catalog_Product_Edit_Tab_Productblogs extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * Set grid params
     *
     */
    public function __construct() {
        parent::__construct();
        $this->setId('productblogsGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultFilter(array('in_productblogs' => 1));
        $this->setDefaultSort('in_productblogs');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('blog/post')
                ->getCollection();
//        $tm_id = $this->getRequest()->getParam('id');
//        if(!isset($tm_id)) {
//            $tm_id = 0;
//        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('in_productblogs', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'values'            => $this->_getSelectedProductBlogs(),
            'align'             => 'center',
            'index'             => 'post_id'
        ));
        
        $this->addColumn('post_id', array(
            'header'    => Mage::helper('blogproducts')->__('Post ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'post_id'
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('blogproducts')->__('Title'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'title'
        ));
        
        $this->addColumn('created_time', array(
            'header'    => Mage::helper('blogproducts')->__('Date'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'created_time'
        ));
        
        $this->addColumn('user', array(
            'header'    => Mage::helper('blogproducts')->__('User'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'user'
        ));
        
        $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Serializer ID'),
            'name'              => 'position',
            'width'             => 80,
            'editable'          => true,
            'edit_only'         => true,
            'index'             => 'position',
            'sortable'          => false,
            'filter'            => false,
        ));

        return parent::_prepareColumns();
    }
    
    /**
     * Add filter
     *
     * @param object $column
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related
     */
    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_productblogs') {
            $blogIds = $this->_getSelectedProductBlogs();
            if (empty($blogIds)) {
                $blogIds = 0;
            }
            
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('post_id', array('in' => $blogIds));
            } else {
                if($blogIds) {
                    $this->getCollection()->addFieldToFilter('post_id', array('nin' => $blogIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Rerieve grid URL
     * @return string
     */
    public function getGridUrl() {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/productblogsgrid', array('_current' => true));
    }

    /**
     * Retrieve selected related blogs
     * @return array
     */
    protected function _getSelectedProductBlogs() {
        $productblogs = array_keys($this->getSelectedProductBlogs());
        return $productblogs;
    }

    /**
     * Retrieve related blogs
     * @return array
     */
    public function getSelectedProductBlogs() {
        $product_id = $this->getRequest()->getParam('id');
        if(!isset($product_id)) {
            $product_id = 0;
        }
		
        $selected_blogs = Mage::getModel('blogproducts/productblogs')
                ->getCollection()
                ->addFieldToFilter('product_id', $product_id);
        $productblogs = array();
        $prodblogIds = array();

        foreach($selected_blogs as $blogs){
            $productblogs[] = $blogs->blog_id;
        }
        
        foreach($productblogs as $productblog) {
            foreach($productblogs as $prodblog) {
                $prodblogIds[$prodblog] = array('position'=>$prodblog);
            }
        }

        return $prodblogIds;
    }

    //Remove row urls to prevent page jumping
    public function getRowUrl($row) {
        return null;
    }
}
