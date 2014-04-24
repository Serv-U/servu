<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    
 * @package     _storage
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ShoppingFeeds Bing feed product grid container
 *
 * @category    ShoppingFeeds
 * @package     ShoppingFeeds_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ShoppingFeeds_Feed_Block_Adminhtml_Sfproducts_Grid  extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid settings
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('shoppingfeeds_feed_sfproducts');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * Return Current work store
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Prepare product collection
     *
     * @return ShoppingFeeds_Feed_Block_Adminhtml_SFProducts_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStore($this->_getStore())
            //->addAttributeToFilter('status', array('eq' => 1))
            ->addAttributeToFilter('visibility', array('eq' => 4))
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('in_shoppingfeeds')
            ->addAttributeToSelect('status');
            $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return ShoppingFeeds_Feed_Block_Adminhtml_SFProducts_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'            => Mage::helper('shoppingfeeds_feed')->__('ID'),
            'sortable'          => true,
            'width'             => '60px',
            'index'             => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'            => Mage::helper('shoppingfeeds_feed')->__('Product Name'),
            'index'             => 'name',
            'column_css_class'  => 'name'
        ));

        $this->addColumn('type', array(
            'header'            => Mage::helper('shoppingfeeds_feed')->__('Type'),
            'width'             => '60px',
            'index'             => 'type_id',
            'type'              => 'options',
            'options'           => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $entityTypeId =  Mage::helper('shoppingfeeds_feed')->getProductEntityType();
        $sets           = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityTypeId)
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header'            => Mage::helper('shoppingfeeds_feed')->__('Attrib. Set Name'),
            'width'             => '100px',
            'index'             => 'attribute_set_id',
            'type'              => 'options',
            'options'           => $sets,
        ));

        $this->addColumn('sku', array(
            'header'            => Mage::helper('shoppingfeeds_feed')->__('SKU'),
            'width'             => '80px',
            'index'             => 'sku',
            'column_css_class'  => 'sku'
        ));

        $this->addColumn('price', array(
            'header'            => Mage::helper('shoppingfeeds_feed')->__('Price'),
            'align'             => 'center',
            'type'              => 'currency',
            'currency_code'     => $this->_getStore()->getCurrentCurrencyCode(),
            'rate'              => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),
            'index'             => 'price'
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
        
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Product Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        
        $source = Mage::getModel('eav/entity_attribute_source_boolean');
        $isImportedOptions = $source->getOptionArray();

        $this->addColumn('in_shoppingfeeds', array(
            'header'    => Mage::helper('shoppingfeeds_feed')->__('In feed'),
            'width'     => '100px',
            'index'     => 'in_shoppingfeeds',
            'type'      => 'options',
            'options'   => $isImportedOptions
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return ShoppingFeeds_Feed_Block_Adminhtml_SFProducts_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('item_id');

        $this->getMassactionBlock()->addItem('enable', array(
            'label'         => Mage::helper('shoppingfeeds_feed')->__('Publish'),
            'url'           => $this->getUrl('*/sfproducts_grid/massEnable'),
            'selected'      => true,
        ));
        $this->getMassactionBlock()->addItem('disable', array(
            'label'         => Mage::helper('shoppingfeeds_feed')->__('Not publish'),
            'url'           => $this->getUrl('*/sfproducts_grid/massDisable'),
        ));

        return $this;
    }

    /**
     * Return Grid URL for AJAX query
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
