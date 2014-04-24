<?php
/**
 *
 * @author Alin_M
 *
 */

class Alin_ProductGift_Helper_Output extends Mage_Catalog_Helper_Output {
	public function productAttribute($product, $attributeHtml, $attributeName) {
		if ($attributeName!='short_description') return parent::productAttribute($product, $attributeHtml, $attributeName);
		$_prod=Mage::getModel('catalog/product')->load($product->getId());
		if (!$_prod->getData('is_product_gift_enabled')) return parent::productAttribute($product, $attributeHtml, $attributeName);
		$html=Mage::helper('productgift')->getBlockGift($_prod->getData('sku_of_product_gift'));
		return '<div>'.parent::productAttribute($product, $attributeHtml, $attributeName).'</div>'.$html;
	}
	
}