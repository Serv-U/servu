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
class SD_Manager_Block_ManufacturerInfo extends Mage_Core_Block_Template
{
    public function getAttributeInfo()
    {
        if (!$this->hasData('manufacturerInfo')) {

            if ($this->getId()) {
                $manufacturerInfo = Mage::getModel('sd_manager/ManufacturerInfo')
                    ->load($this->getId());
            } else {
                $manufacturerInfo = Mage::getSingleton('sd_manager/manufacturer');
            }
            $this->setData('manufacturerInfo', $manufacturerInfo);
        }
        return $this->getData('manufacturerInfo');
    }

    public function getDataOr($data, $default) 
    {
    	if ($res = $this->getData($data)) {
            return $res;
        } else {
            return $default;
        }
    }

    protected function _prepareLayout()
    {
        $manufacturerInfo = $this->getAttributeInfo();
        
        // show breadcrumbs
        if (Mage::getStoreConfig('web/default/show_cms_breadcrumbs')
            && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))) {

            	$breadcrumbs->addCrumb(
                	'home',
                	array(
                		'label'=>Mage::helper('sd_manager')->__('Home'),
                		'title'=>Mage::helper('sd_manager')->__('Go to Home Page'),
                		'link'=>Mage::getBaseUrl()
                	)
                );
	            $breadcrumbs->addCrumb('allvalues', array(
                    'label' => Mage::helper('sd_manager')->__(ucfirst($manufacturerInfo->getAttributeCode()).'s'),
                    'link'  => Mage::getUrl(''.$manufacturerInfo->getAttributeCode().'/'),
                ));

                $breadcrumbs->addCrumb('sd_manager', array('label'=>$manufacturerInfo->getValue(), 'title'=>$manufacturerInfo->getValue()));
        }

        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('attribute-info-'.$manufacturerInfo->getAttributeCode());
        }

        if ($head = $this->getLayout()->getBlock('head')) {
            
            if ($manufacturerInfo->getPageTitle() > '') {           
                $head->setTitle($manufacturerInfo->getPageTitle());
            } else  {
                $head->setTitle($manufacturerInfo->getValue());
            }
            $head->setKeywords($manufacturerInfo->getMetaKeywords());
            $head->setDescription($manufacturerInfo->getMetaDescription());
        }
    }

    public function getDescription() {
        $content = $this->getAttributeInfo()->getDescription();
        $processor = Mage::getModel('core/email_template_filter');
        
        return $processor->filter($content);
    }
    
    //DM 11-26-2013 Added simple banner functionality
    public function getBanner() {
        $content = $this->getAttributeInfo()->getBanner();
        $processor = Mage::getModel('core/email_template_filter');
        
        return $processor->filter($content);
    }

    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }

    /**
     * return the current page
     * show the logo/description only on the first page
     */
    public function getCurrentPage() {

        $layer = Mage::getSingleton('catalog/layer');
        /* @var $layer Mage_Catalog_Model_Layer */
        if ($layer) {
        	return $layer->getProductCollection()->getCurPage();
        }
        return false;

    }

}