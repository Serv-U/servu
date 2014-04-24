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
 * ShoppingFeeds Feed product grid controller
 *
 * @category    ShoppingFeeds
 * @package     ShoppingFeeds_Feed
 */
class ShoppingFeeds_Feed_Adminhtml_Sfproducts_GridController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Main index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->loadLayout();    
        $this->getResponse()->setBody($this->getLayout()->createBlock('shoppingfeeds_feed/adminhtml_sfproducts_grid')->toHtml());
    }
    
    /**
     * Product list for mass action
     *
     * @return array
     */
    protected function _getMassActionProducts()
    {
        $idList = $this->getRequest()->getParam('item_id');
        if (!empty($idList)) {   
            $products = array();
            foreach ($idList as $id) {
                $model = Mage::getModel('catalog/product');
                if ($model->load($id)) {
                    array_push($products, $model);
                }
            }
            return $products;
        } else {
            return array();
        }
    }

    /**
     * Add product to feed mass action
     */
    public function massEnableAction()
    {
        $idList = $this->getRequest()->getParam('item_id');
        $updateAction = Mage::getModel('catalog/product_action');
        $attrData = array(
            'in_shoppingfeeds' => 1
        );
        $updatedProducts = count($idList);
        if ($updatedProducts) {
            try {
                $updateAction->updateAttributes($idList, $attrData, Mage::app()->getStore()->getId());
                //Disabled generation of feed from running everytime Products grid is updated
                //Mage::getModel('shoppingfeeds_feed/thefindimport')->processImport();
                //Mage::getModel('shoppingfeeds_feed/bingimport')->processImport();
                $this->_getSession()->addSuccess(Mage::helper('shoppingfeeds_feed')->__("%s products added to feeds.", $updatedProducts));
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('shoppingfeeds_feed')->__("Unable to add products to feeds. ") . $e->getMessage());
            } 
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Not add product to feed mass action
     */
    public function massDisableAction()
    {
        $idList = $this->getRequest()->getParam('item_id');
        $updateAction = Mage::getModel('catalog/product_action');
        $attrData = array(
            'in_shoppingfeeds' => 0
        );
        $updatedProducts = count($idList);
        
        if ($updatedProducts) {
            try {
                $updateAction->updateAttributes($idList, $attrData, Mage::app()->getStore()->getId());
                //Disabled generation of feed from running everytime Products grid is updated
                //Mage::getModel('shoppingfeeds_feed/thefindimport')->processImport();
                //Mage::getModel('shoppingfeeds_feed/bingimport')->processImport();
                $this->_getSession()->addSuccess(Mage::helper('shoppingfeeds_feed')->__("%s product removed from feed.", $updatedProducts));
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('shoppingfeeds_feed')->__("Unable to remove products from feeds.") . $e->getMessage());
            } 
        } 
        $this->_redirect('*/*/index');
    }
}
