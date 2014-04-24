<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManufacturerInfo
 *
 * @author dustinmiller
 */
class SD_Manager_Helper_ManufacturerInfo 
    extends Mage_Core_Helper_Abstract
{
    /**
     * The attribute page model
     * @var SD_Manager_Helper_ManufacturerInfo
     */
    protected $_attributePage;
    /**
     * Retrieve attribute page controller
     * @return SD_Manager_Model_Manufacturer
     */

    public function getAttributePage()
    {
        if (!$this->_attributePage) {
            $this->_attributePage = Mage::getSingleton('sd_manager/manufacturer');
        }

        return $this->_attributePage;
    }

    /**
    * Load the information about the requested attribute page
    *
    * @param Mage_Core_Controller_Front_Action $action
    * @param integer $pageId
    * @return boolean
    */
    public function loadAttributePage(Mage_Core_Controller_Front_Action $action, $manufacturerInfoId = null, $attribute_code = null, $option_id = null)
    {
        $attributeInfo = $this->getAttributePage();
        //show all products from this store
        $rootCategory = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId());

        //current layer navigation
        $layer = Mage::getSingleton('catalog/layer');
        /* @var $layer SD_Manager_Model_Layer */
        $layer->setCurrentCategory($rootCategory);
        
        //the product collection that will be further filtered
        $collection = $layer->getProductCollection();
	/* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        //set the store id and the main category from the store
        $collection->setStoreId(Mage::app()->getStore()->getId());
        
        if (!is_null($manufacturerInfoId) && ($manufacturerInfoId!==$attributeInfo->getId())) {
        //load the info from id
            if($manufacturerInfoId > 0) {
                $attributeInfo->load($manufacturerInfoId);
            }
        }

	if ($attributeInfo->getData('attribute_code') > '') {
            //filter the products by the manufacturer ID
            $attributeModel = Mage::getSingleton('catalog/resource_eav_attribute');
            $attributeModel->load($attributeInfo->getData('attribute_code'), 'attribute_code');
	} else {
	//id not found, try to load from the attribute tables
            $attributeInfo->loadFromAttribute($attribute_code, $option_id, Mage::app()->getStore()->getId());
            
            if ($attributeInfo->getData('attribute_code') > '') {
                $attributeModel = Mage::getSingleton('catalog/resource_eav_attribute');
		$attributeModel->load($attributeInfo->getData('attribute_code'), 'attribute_code');
            } else {
                return false;
            }
	}

	$attributeValue = $attributeInfo->getData('attribute_option_id') > 0 ? $attributeInfo->getData('attribute_option_id') : $attributeInfo->getData('option_id');
	if ('multiselect' == $attributeModel->getData('frontend_input')) {
            
            $attributeValue = array(
                array("finset" => $attributeValue)
            );
	}

        Mage::register('attribute_code', $attributeInfo->getData('attribute_code'));
	//with this type of filter, the attribute doesn't need to be filterable in layered navigation.. cool ;)
	//Flat product issue
        //$collection->addAttributeToSelect($attributeInfo->getData('attribute_code'))//->addAttributeToFilter($attributeValue);
            //->addAttributeToFilter($attributeModel,$attributeValue);
        //$collection = Mage::getModel('catalog/product')->getCollection();
        $table = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'manufacturer')->getBackend()->getTable();
        $attributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'manufacturer')->getAttributeId();
        $collection->getSelect()->join(array('attributeTable' => $table), 'e.entity_id = attributeTable.entity_id', array('manufacturer' => 'attributeTable.value'))
                            ->where("attributeTable.attribute_id = ?", $attributeId)
                            ->where("attributeTable.value = ?", $attributeValue);
        Mage::register('attribute_value_activation_flag', $attributeValue);

        //try to find a root category that will display more then 1 category for filtering (it's useless to see always just one category)
	$lastCat = $layer->getCurrentCategory();
	do {
            $categories = $lastCat->getChildrenCategories();
            $layer->getProductCollection()->addCountToCategories($categories);
            $cntCategories = 0;

            foreach ($categories as $category) {
                if ($category->getIsEnabled() && $category->getProductCount()) {
                        $lastCat = $category;
                	$cntCategories++;
                }
            }

            if (1 == $cntCategories) {
                $layer->setCurrentCategory($lastCat);
                $collection = $layer->getProductCollection();
		$collection->setStoreId(Mage::app()->getStore()->getId());
		$collection->addAttributeToSelect($attributeInfo->getData('attribute_code'))
                    ->addAttributeToFilter($attributeModel,$attributeValue);
            }

	} while ((1 == $cntCategories) && (0 != $cntCategories));
	//echo $collection->getSelect();
        return true;
    }
    /**
    * Renders the attribute info page
    * Call from controller action
    *
    * @param Mage_Core_Controller_Front_Action $action
    */

    public function renderAttributePage(Mage_Core_Controller_Front_Action $action)
    {
        $attributeInfo = $this->getAttributePage();
    	if ($attributeInfo->getCustomTheme()) {
            if (Mage::app()->getLocale()->IsStoreDateInInterval(null, $attributeInfo->getCustomThemeFrom(), $attributeInfo->getCustomThemeTo())) {
                list($package, $theme) = explode('/', $attributeInfo->getCustomTheme());
                Mage::getSingleton('core/design_package')
                    ->setPackageName($package)
                    ->setTheme($theme);
            }
        }

        $action->getLayout()->getUpdate()
            ->addHandle('default')
            ->addHandle('sd_manager_manufacturerinfo');

        $action->addActionLayoutHandles();

        if (($attributeInfo->getRootTemplate()) && ('empty' != $attributeInfo->getRootTemplate())) {
            $action->getLayout()->helper('page/layout')
                ->applyHandle($attributeInfo->getRootTemplate());
        }

        $action->loadLayoutUpdates();
        $action->getLayout()->getUpdate()->addUpdate($attributeInfo->getLayoutUpdateXml());
        $action->generateLayoutXml()->generateLayoutBlocks();

        // show breadcrumbs
        if (Mage::getStoreConfig('web/default/show_cms_breadcrumbs')
            && ($breadcrumbs = $action->getLayout()->getBlock('breadcrumbs'))) {
            $breadcrumbs->addCrumb('home', array(
                'label'=>Mage::helper('sd_manager')->__('Home'),
                'title'=>Mage::helper('sd_manager')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
                )
            );

            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributeInfo->getAttributeCode());
            if (is_array($labels = $attribute->getStoreLabels()) && (isset($labels[Mage::app()->getStore()->getId()]))) {
                $label = $labels[Mage::app()->getStore()->getId()];
            } else {
                $label = $attribute->getFrontendLabel();
            }

            $breadcrumbs->addCrumb('allvalues', array(
                'label' => Mage::helper('sd_manager')->__($label.'s'),
                'link'  => Mage::getUrl(''.$attributeInfo->getAttributeCode().'/'),
            ));

           $breadcrumbs->addCrumb('sd_manager', array('label'=>$attributeInfo->getValue(), 'title'=>$attributeInfo->getValue()));
        }
        
        if (($attributeInfo->getRootTemplate()) && ('empty' != $attributeInfo->getRootTemplate())) {
            $action->getLayout()->helper('page/layout')
                ->applyTemplate($attributeInfo->getRootTemplate());
        }

        if ($storage = Mage::getSingleton('catalog/session')) {
            $action->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }

        if ($storage = Mage::getSingleton('checkout/session')) {
            $action->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }

        $action->renderLayout();

        return true;
    }

    /**
    * Load the information about the requested attribute page
    *
    * @param Mage_Core_Controller_Front_Action $action
    * @param integer $pageId
    * @return boolean
    */

    public function loadAllAttributesPage(Mage_Core_Controller_Front_Action $action, $attribute_code = null)
    {
        //the attribute value collection
        $collection = Mage::getModel('sd_manager/manufacturer')->getCollection();
        //set the store id and the main category from the store
        $collection->addStoreFilter(Mage::app()->getStore()->getId(), true)
            ->addEnabledFilter();
        return true;
    }

    /**
    * Renders the attribute info page
    * Call from controller action
    *
    * @param Mage_Core_Controller_Front_Action $action
    */

    public function renderAllAttributesPage(Mage_Core_Controller_Front_Action $action)
    {
        $action->getLayout()->getUpdate()
            ->addHandle('default')
            ->addHandle('sd_manager_manufacturerinfo_all');

        $action->addActionLayoutHandles();
        $action->loadLayoutUpdates();
        //$action->getLayout()->getUpdate()->addUpdate($attributeInfo->getLayoutUpdateXml());
        $action->generateLayoutXml()->generateLayoutBlocks();

        /*if (($attributeInfo->getRootTemplate()) && ('empty' != $attributeInfo->getRootTemplate())) {
            $action->getLayout()->helper('page/layout')
                ->applyTemplate($attributeInfo->getRootTemplate());
        }*/
        // show breadcrumbs

        if (Mage::getStoreConfig('web/default/show_cms_breadcrumbs')
            && ($breadcrumbs = $action->getLayout()->getBlock('breadcrumbs'))) {
            
            $breadcrumbs->addCrumb('home',
                array(
                    'label'=>Mage::helper('sd_manager')->__('Home'),
                    'title'=>Mage::helper('sd_manager')->__('Go to Home Page'),
                    'link'=>Mage::getBaseUrl()
                )
            );
            
            $attributeCode = Mage::registry('attribute_code');
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributeCode);

            if (is_array($labels = $attribute->getStoreLabels()) && (isset($labels[Mage::app()->getStore()->getId()]))) {
                $label = $labels[Mage::app()->getStore()->getId()];
            } else {
                $label = $attribute->getFrontendLabel();
            }

            $breadcrumbs->addCrumb('allvalues', array(
                'label' => Mage::helper('sd_manager')->__($label.'s'),
            ));
        }

        if ($storage = Mage::getSingleton('catalog/session')) {
            $action->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }

        if ($storage = Mage::getSingleton('checkout/session')) {
            $action->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }

        $action->renderLayout();
        return true;
    }

}

?>