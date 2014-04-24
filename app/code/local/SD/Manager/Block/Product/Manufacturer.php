<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manufacturer
 *
 * @author dustinmiller
 */
class SD_Manager_Block_Product_Manufacturer 
    extends Mage_Core_Block_Template
{
    /* @var $_product Mage_Catalog_Model_Product */
    protected $_product = null;
    protected $_product_attributes = null;
    /**
     * the currently viewed product
     * @return Mage_Catalog_Model_Product
     */
    function getProduct()
    {
        if (!$this->_product) {
            if (!($this->_product = $this->getData('product'))) {
                $this->_product = Mage::registry('product');
            }
        }
        return $this->_product;
    }

    function getProductAttributes()
    {
        if (!$this->_product_attributes) {
            $this->_product_attributes = $this->getProduct()->getAttributes();
        }
        return $this->_product_attributes;
    }

    protected function _loadAttributeData($attributeCode, $attributeOptionId) {
	/* @var $model DC_Catalog_Model_Manufacturer */
    	$model = Mage::getModel('sd_manager/manufacturer');
        $model->loadFromAttribute($attributeCode, $attributeOptionId, Mage::app()->getStore()->getId());
        return $model;
    }

    public function getAttributeInfo($attributeCode = 'manufacturer', $attributeOptionId = false) {
        $attributes = $this->getProductAttributes();
        if (!isset($attributes[$attributeCode])) {
        	return false;
        }

    	if (false === $attributeOptionId) {
    		$attributeOptionId = $this->getProduct()->getData($attributeCode);
    	}
        return $this->_loadAttributeData($attributeCode, $attributeOptionId);
    }

    public function getDataOr($data, $default) {
    	if ($res = $this->getData($data))
    		return $res;
    	else
    		return $default;
    }

    /**
     * $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     */

    public function getAdditionalData(array $excludeAttr = array())
    {
        $data = array();
        $product = $this->getProduct();
        /* @var $product Mage_Catalog_Model_Product */
        $attributes = $product->getAttributes();

        foreach ($attributes as $attribute) {
            //if ($attribute->getIsVisibleOnFront() && $attribute->getIsUserDefined() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                /*$value = $attribute->getFrontend()->getValue($product);
                // TODO this is temporary skipping eco taxes
                if (is_string($value)) {
                    if (strlen($value) && $product->hasData($attribute->getAttributeCode())) {
                        if ($attribute->getFrontendInput() == 'price') {
                            $value = Mage::app()->getStore()->convertPrice($value,true);
                        } elseif (!$attribute->getIsHtmlAllowedOnFront()) {
                            $value = $this->htmlEscape($value);
                        }
                        $data[$attribute->getAttributeCode()] = array(
                           'label' => $attribute->getFrontend()->getLabel(),
                           'value' => $value,
                           'code'  => $attribute->getAttributeCode()
                        );
                    }
                }*/
            }
        }
        return $data;
    }
}
?>