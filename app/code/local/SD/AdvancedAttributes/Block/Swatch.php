<?php
class SD_AdvancedAttributes_Block_Swatch extends Mage_Core_Block_Template
{
    public function getSwatchImages(){
        $data = $this->getRequest()->getParams();
        $product = Mage::getModel('catalog/product')->load($data['id']);
		$swatch = '';
        
		if($product->isConfigurable()) {
			$productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
	        $attributeOptions = array();
	        $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/attributes/configurables/view/';
			$resizedUrl = $mediaUrl . 'resized/';
	        $swatchModel = Mage::getModel('advancedattributes/configurableOptions');
	        foreach ($productAttributeOptions as $productAttribute) {
				$numberOfImages = 0;
				$swatch .= '<div class="swatch-gallery"><h2>'.$productAttribute['label'].'</h2><ul>';
	            foreach ($productAttribute['values'] as $attribute) {
					$swatchModel->loadFromOptionId($attribute['value_index']);
					if($swatchModel->getProductViewImage() != '') {
						$numberOfImages++;
						$thumbHtml = "<img alt='".$attribute['store_label']."' title='".$attribute['store_label']."' width='30' height='30' src='".$resizedUrl.$swatchModel->getProductViewImage()."'>";
		                $imageHtml = $mediaUrl.$swatchModel->getProductViewImage();
						//preparing li for swatches on product detail page
		                $swatch .= "<li> <a href='".$imageHtml."' rel='prettyPhoto[".$productAttribute['id']."]'>".$thumbHtml."</a></li>";	
					}
	            }
				$swatch .= '</ul></div>';
				if($numberOfImages == 0) {
					$swatch = '';	
				}
	        }	
		}
                    
        return $swatch;
    }
}
?>