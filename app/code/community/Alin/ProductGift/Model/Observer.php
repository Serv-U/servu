<?php
/**
 *
 * @author alin_M
 *
 */

class Alin_ProductGift_Model_Observer {
	protected $_dsr=null;
	
	public function addGift($observer)
	{
		$p=$observer->getEvent()->getProduct();
		$q=$observer->getEvent()->getQuoteItem();
		/*
		if ($q->getParentItem()) {
			$q = $q->getParentItem();
		}
		*/
		$idd=$q->getProduct()->getId();
		if (!$p->getData('is_product_gift_enabled')) return $this;
		$obj=Mage::getSingleton('checkout/cart');
		$qu=$obj->getQuote();
		$sku_gift=$p->getData('sku_of_product_gift');
		if ($q->getParentItem()) {
			$qty_c=$q->getParentItem()->getQty();
			$qty_a=$q->getParentItem()->getQtyToAdd();
		} else
		{
			$qty_c=$q->getQty();
			$qty_a=$q->getQtyToAdd();
		}
		if ($qty_c != $qty_a) { 
			
			foreach ($qu->getItemsCollection() as $it) {
				
				$ops=$it->getOptionByCode('gift_for_product_id');
				if($ops) {
					if ($ops->getValue()==$q->getProduct()->getId()) {
						$it->setQty($qty_c);
						$qu->save();
						return $this;
					}
					
				}
				
			} 
		}
		
		$prod_gift = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku_gift);
		$x=array($prod_gift->getId());
		$oldi=$qu->getItemByProduct($prod_gift);
		if ($oldi) {  
			$option = Mage::getModel('sales/quote_item_option')
			->setProductId($prod_gift->getId())
			->setCode('gift_for_product_id')
			->setProduct($prod_gift)
			->setValue(null);
			$oldi->addOption($option);
			$qu->save();
		}
		
		$gicu=$obj->addProductsByIds($x);
		$li=$qu->getItemByProduct($prod_gift);
		$option = Mage::getModel('sales/quote_item_option')
		->setProductId($prod_gift->getId())
		->setCode('gift_for_product_id')
		->setProduct($prod_gift) 
		->setValue($q->getProduct()->getId());
		
		$li->addOption($option);
		$options=$li->getOptionsByCode();
		
		$li->setCustomPrice(0);
		$li->setOriginalCustomPrice(0);
		$li->getProduct()->setIsSuperMode(true);
		$li->setQty($qty_c);
		//$li->setMessage('This is a gift!');
		$qu->save();
		
		return $this;
	}
	
	public function updtatePGiftA($observer) {
		$c=$observer->getEvent()->getCart();
		$dat=$observer->getEvent()->getInfo();
		$prev_i=null;
		foreach ($dat as $itemId => $itemInfo) {
			$i = $c->getQuote()->getItemById($itemId);
			$old_q = $i->getOrigData('qty');
			$qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
			$ops=$i->getOptionByCode('gift_for_product_id');
			if ($ops && $ops->getValue()) { 
					if ($prev_i->getOrigData('qty')!=$prev_i->getQty()) { 
						$i->setQty($prev_i->getQty());
					} elseif ($qty!=$i->getOrigData('qty')) { 
						Mage::throwException('You may not change the number of free products received');
					}
				
			}
			$prev_i=$i;
				
		}
	
		return $this;
	}
	
	public function deletePGift($observer) {
		
		$qi=$observer->getEvent()->getQuoteItem(); 
		$p= Mage::getModel('catalog/product')->load($qi->getProduct()->getId() );
		$cil=$qi->getChildren();
		if (!empty($cil)) {
			$idd= current($qi->getChildren())->getProduct()->getId() ;
		} else {
			$idd=$qi->getProduct()->getId() ;
		}
		$is = $p->getData('is_product_gift_enabled');
		if (!$is) return $this;
		
		$obj=Mage::getSingleton('checkout/cart');
		$qu=$obj->getQuote();
		foreach ($qu->getItemsCollection() as $it) {
			
			$ops=$it->getOptionByCode('gift_for_product_id');
			if($ops) {
				if ($ops->getValue()==$idd) {
					$obj->removeItem($it->getItemId());
					$qu->save();
					return $this;
				}
					
			}
		}
		return $this;
	}
	public function checkSKU($observer) {
		$p = $observer->getEvent()->getProduct();
		if (!$p->getData('is_product_gift_enabled')) return $this;
		$sku_gift=$p->getData('sku_of_product_gift');
		if (!Mage::getModel('catalog/product')->loadByAttribute('sku', $sku_gift)) Mage::throwException('SKU for gift is invalid!');
			
	}
}