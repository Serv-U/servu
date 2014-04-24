<?php
/**
 *
 * @author Alin_M
 *
 */

class Alin_ProductGift_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getBlockGift($sku_gift) {
		//$prod_gift = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku_gift);
		//$gift_name = $prod_gift->getName();
		//$gift_image = Mage::helper('catalog/image')->init($prod_gift, 'small_image')->resize(78);
		//$gift_url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		//$gift_url.=$prod_gift->getUrlPath();
		//$gift_price=$prod_gift->getFormatedPrice();
		//$url_img=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
		
                $html='';
                
		//$html='<div class="product_gift" style="float:left;"><table border="0" cellspacing="0" cellpadding="0"><tr>';
		//$html.='<td width="38" style="height:80px; background:url(\''.Mage::getDesign()->getSkinUrl('images/gift2e.png').'\');"></td>';
		//$html.='<td> <img src="'.$gift_image.'" style="border-top:1px solid #646464; border-bottom:1px solid #646464;"> </td>';
		//$html.='<td><div style="line-height:1.1; width:140px; height:78px; border-top:1px solid #646464; border-bottom:1px solid #646464; border-right:1px solid #646464;">';
		//$html.='<div style="height:50px; overflow:hidden;"><a href="'.$gift_url.'">'.$gift_name.'</a></div>';
		//$html.='<div style="float:bottom;"><b>Gift value: '.$gift_price.'</b></div>';
		//$html.='</div></td></tr></table></div>';
		return $html;
	}
}
