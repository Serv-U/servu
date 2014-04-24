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
 * @package     Mage_Page
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class ServU_Extjs_Block_Product_View extends Mage_Catalog_Block_Product_View
{
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('catalog/breadcrumbs');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $product = $this->getProduct();
            $title = $product->getMetaTitle();

            /*  
             * DM 2/14/11 - Custom Code
             * If there is not a meta title set, then lets create one with this structure:
             * <Manufacturer Name> <Manufacturer Sku> <Product Name> 
             * Ignore attribute if it is empty
             */
            if(!isset($title) || trim($title) === '') {
                $title = $product->getAttributeText('manufacturer');

                if($product->getManufacturerSku()) {
                    if($title) {
                        $title .= ' ' . $product->getManufacturerSku();
                    } 
                    else {
                        $title = $product->getManufacturerSku();
                    }
                }
                if($product->getName()) {
                    if($title) {
                        $title .= ' ' . $product->getName();
                    } 
                    else {
                        $title = $product->getName();
                    }
                }
                //End Custom Code 
            }
            $headBlock->setTitle($title);
            
            /*  
             * DM 2/14/11 - Custom Code
             * Get the products tags and set them as the meta keywords.
             * If those are empty, just use the normal procedure to set
             * meta keywords.
             */
            $tagModel = Mage::getModel('tag/tag');
            $tags = $tagModel->getResourceCollection()
                ->addPopularity()
                ->addStatusFilter($tagModel->getApprovedStatus())
                ->addProductFilter($product->getId())
                ->setFlag('relation', true)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->setActiveFilter()
                ->load();

            $keyword = '';
            foreach ($tags as $tag) { 
                $keyword .= $tag->getName() . ', ';
            }
            $keyword = rtrim($keyword, ', ');
            
            if(!isset($keyword) || trim($keyword) === ''){
                $currentCategory = Mage::registry('current_category');
                $keyword = $product->getMetaKeyword();
            
                if ($keyword) {
                    $headBlock->setKeywords($keyword);
                } elseif ($currentCategory) {
                    $headBlock->setKeywords($product->getName());
                }
            }
            else {
                $headBlock->setKeywords($keyword);
            }
            
            $description = $product->getMetaDescription();
            
            /*  
             * DM 2/14/11 - Custom Code
             * If there is not a meta title set, then lets create one with this structure:
             * <Manufacturer Name> <Manufacturer Sku> <Product Name> 
             * Ignore attribute if it is empty
             */
            if(!isset($description) || trim($description) === '') {
                $description = $product->getAttributeText('manufacturer');

                if($product->getManufacturerSku()) {
                    if($description) {
                        $description .= ' ' . $product->getManufacturerSku();
                    } 
                    else {
                        $description = $product->getManufacturerSku();
                    }
                }
                if($product->getName()) {
                    if($description) {
                        $description .= ' ' . $product->getName();
                    } 
                    else {
                        $description = $product->getName();
                    }
                }
                $prefix = Mage::getStoreConfig('design/head/product_page_title_prefix');
                $suffix = Mage::getStoreConfig('design/head/product_page_title_suffix');

                if(isset($prefix) && trim($prefix) != ''){
                    $description = $prefix . ' ' . $description;
                }
                if(isset($suffix) && trim($suffix) != ''){
                    $description .= ' ' . $suffix;
                }
                //End Custom Code 
            }
            
            $headBlock->setDescription($description);

            if ($this->helper('catalog/product')->canUseCanonicalTag()) {
                $params = array('_ignore_category' => true);
                $headBlock->addLinkRel('canonical', $product->getUrlModel()->getUrl($product, $params));
            }
        }

        return $this;
    }
}
