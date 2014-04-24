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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog view layer model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SD_AdvancedAttributes_Model_Catalog_Layer extends Mage_Catalog_Model_Layer
{

    protected $productCollection;
    
    public function getProductCollection() {
        if (!$this->productCollection) {
            $collection = Mage::getResourceModel('advancedattributes/product_collection');
            if ($this->getCurrentCategory()->getIsAnchor()) {
                    $collection->setStoreId(Mage::app()->getStore()->getId())
                                ->addCategoryFilter($this->getCurrentCategory());
            }
            
            if(Mage::registry('attribute_value_activation_flag')){
                $attributeValue = Mage::registry('attribute_value_activation_flag');
                $table = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'manufacturer')->getBackend()->getTable();
                $attributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'manufacturer')->getAttributeId();
                $collection->getSelect()->join(array('attributeTable' => $table), 'e.entity_id = attributeTable.entity_id', array('manufacturer' => 'attributeTable.value'))
                            ->where("attributeTable.attribute_id = ?", $attributeId)
                            ->where("attributeTable.value = ?", $attributeValue);
            }

            $this->prepareProductCollection($collection);
            $this->productCollection = $collection;
        }

        return $this->productCollection;
    }
    
    public function prepareProductCollection($collection) {
            $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents();
            $queryText = Mage::helper('catalogsearch')->getQueryText();
            if (!$this->getCurrentCategory()->getIsAnchor() || !empty($queryText)) {
                
                    if(Mage::helper('catalogsearch')->getQuery()->getQueryText() != '') {
                        $uid = Mage::helper('mstcore/debug')->start();
                        /*$collection->addSearchFilter(Mage::helper('catalogsearch')->getQuery()->getQueryText())
                            ->setStore(Mage::app()->getStore())
                            ->addStoreFilter()
                            ->addUrlRewrite();*/
                            $collection
                                //->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                                ->setStore(Mage::app()->getStore())
                                //->addMinimalPrice()
                                //->addFinalPrice()
                                //->addTaxPercents()
                                ->addStoreFilter()
                                ->addUrlRewrite();

                            $catalogIndex = Mage::helper('searchindex/index')->getIndex('mage_catalog_product');
                            $catalogIndex->joinMatched($collection);

                            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
                            Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);

                            //$this->_addStockOrder($collection);

                            Mage::helper('mstcore/debug')->dump($uid, array('collection_sql', $collection->getSelect()->__toString()));
                            Mage::helper('mstcore/debug')->end($uid, $this);
                    }
                    
            } else {
                    $collection->addUrlRewrite($this->getCurrentCategory()->getId());
            }

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);

            return $this;
    }
    
    public function getFilterableAttributes() {
        //$entity = Mage::getSingleton('eav/config')
        //->getEntityType('catalog_product');
        $setIds = $this->_getSetIds();
        if (!$setIds) {
            return array();
        }
        /** @var $collection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
        $collection->addSetInfo(true);
        $collection
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }
  
}
