<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of All
 *
 * @author dustinmiller
 */
class SD_Manager_Block_List_All 
    extends Mage_Core_Block_Template
{
    /* @var $_valuesCollection SD_Manager_Model_Mysql4_Manufacturer_Collection */
    protected $_valuesCollection;
    protected $_attributeCode = null;

    public function getAttributeCode() 
    {
    	if (!$this->_attributeCode) {
            if (!$this->_attributeCode = $this->getData('attribute_code')) {
                if (!$this->_attributeCode = Mage::registry('attribute_code')) {
                    if (!$this->_attributeCode = $this->getData('default_attribute_code')) {
                        $this->_attributeCode = 'manufacturer';
                    }
	        }
            }
    	}

    	return $this->_attributeCode;
    }

    /**
     * @return SD_Manager_Model_Mysql4_Manufacturer_Collection
     */

    public function getValuesCollection()
    {
        if (null === $this->_valuesCollection) {
            //the attribute value collection
            $this->_valuesCollection = Mage::getModel('sd_manager/manufacturer')->getCollection();
            //set the store id and the main category from the store
            $this->_valuesCollection
                ->addStoreFilter(Mage::app()->getStore()->getId(), true)
                ->addAttributeCodeFilter($this->getAttributeCode())
	        ->addEnabledFilter();
        }

        return $this->_valuesCollection;
    }

    public function getDataOr($data, $default) {
    	if ($res = $this->getData($data))
    		return $res;
    	else
    		return $default;
    }
    
    
    protected function _prepareLayout()
    {         
        /*if ($head = $this->getLayout()->getBlock('head')) {        
            $head->setTitle(Mage::app()->getStore()->getCode() . ' Manufacturers');
            $head->setKeywords('Test');
            $head->setDescription('Description Test');
        }*/
    } 
    
    protected function _toHtml()
    {
        /*if ($toolbar = $this->getChild('toolbar')) {
            $toolbar->setCollection($this->getValuesCollection());
        }*/
        return parent::_toHtml();
    }
}

?>