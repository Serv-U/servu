<?php
/**
 * 
 * @author Alin_M
 *
 */
class Alin_ProductGift_Helper_Image extends Mage_Catalog_Helper_Image {
	
	public function resize($width, $height = null) {
		
		$p=Mage::getModel('catalog/product')->load($this->getProduct()->getId());
		if (!$p->getData('is_product_gift_enabled')) return parent::resize($width,$height);
		$type_img=$this->_getModel()->getDestinationSubdir();
		if ($type_img!='small_image' /*|| $width!=155*/) return parent::resize($width,$height);
		parent::resize($width,$height);
		$w_w=(int)($width/3.4);
		$img_size=$w_w.'x'.$w_w;
		$this->watermark('freepopcorn.gif', 'bottom-right',$img_size,'100');
		return $this;
	}
}