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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ServU_BlogProducts_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action {
//Mage_Adminhtml_Catalog_ProductController

    /**
     * Get related products grid and serializer block
     */
    public function productblogsAction() {
//        $this->_initProduct();
        $this->loadLayout();
//        $this->getLayout()->getBlock('catalog.product.edit.tab.productblogs')
//            ->setProductsBlogs($this->getRequest()->getPost('productsblogs', null));
        $this->renderLayout();
    }

    /**
     * Get related products grid
     */
    public function productblogsgridAction() {
//        $this->_initProduct();
        $this->loadLayout();
//        $this->getLayout()->getBlock('catalog.product.edit.tab.productblogs')
//            ->setProductsBlogs($this->getRequest()->getPost('productsblogs', null));
//        $this->getLayout()->getBlock('catalog.product.edit.tab.productblogs')
//            ->setProductBlogs($this->getRequest()->getPost('productblogs', null));
        $this->renderLayout();
    }
}
