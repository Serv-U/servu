<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigurablePriceUpdate
 *
 * @author dustinmiller
 */
class SD_ConfigurablePriceUpdate_Model_ConfigurablePriceUpdate {

    public function update() {
        $model = Mage::getModel("catalog/product"); 
        $products = $model->getCollection();
        $products->addAttributeToFilter('status', 1);//enabled
        $products->addAttributeToFilter('type_id', array('eq' => 'configurable'));
        $products->addAttributeToFilter('visibility', 4);//catalog, search
        $products->addAttributeToFilter('attribute_set_id', array('neq' => array(20,120,66,121)));
        //$products->addAttributeToFilter('sku', array('eq' => 'TREM-5013'));
        $products->addAttributeToSelect('*');
        $count = 0;
        
        Mage::log(count($products));
        
        foreach($products as $product) {
            $count++;
            $configurableAttributeCollection = $product->getTypeInstance()->getConfigurableAttributes();

            $configurablePrice = $product->getPrice();
            //Mage::log('Product SKU: '.$product->getSku());
            //Mage::log('Configurable Price: '.$configurablePrice);

            $associatedProducts = $product->getTypeInstance()->getUsedProducts();
            $stack = array();
            $priceMatrix = array();
            for($j=0; $j< sizeof($associatedProducts) ; $j++){
                //Mage::log('Associated SKU: '.$associatedProducts[$j]['sku']);  
                //Mage::log('Associated Price: '.$associatedProducts[$j]['price']);

                foreach($configurableAttributeCollection as $collectionAttribute) {
                    $attributeCode = $collectionAttribute->getProductAttribute()->getAttributeCode();
                    //Mage::log('Price: '.$associatedProducts[$j]['price']);
                    //Mage::log('Code: '.$associatedProducts[$j][$attributeCode]);
                    if(!array_key_exists($associatedProducts[$j][$attributeCode], $priceMatrix) || $priceMatrix[$associatedProducts[$j][$attributeCode]] > abs($associatedProducts[$j]['price']-$configurablePrice)) {
                        $priceMatrix[$associatedProducts[$j][$attributeCode]] = $associatedProducts[$j]['price']-$configurablePrice;
                    }
                    array_push($stack,  Array('configurable'=>$associatedProducts[$j][$attributeCode], 'price'=>$associatedProducts[$j]['price']-$configurablePrice));
                }

            }

            if ($data = $product->getTypeInstance()->getConfigurableAttributesAsArray(($product))) {

                foreach ($data as $attributeData) {

                    $id = isset($attributeData['id']) ? $attributeData['id'] : null;

                    $size = sizeof($attributeData['values']);

                    for($j=0; $j< $size ; $j++){
                        $this->multiArrayValueSearch($stack, $attributeData['values'][$j]['value_index'], $match);
                        if(is_array($match)) {
                            reset($match); // make sure array pointer is at first element
                            $firstKey = key($match);
                        }
                        $match= array();
                        $attributeData['values'][$j]['pricing_value'] = $priceMatrix[$attributeData['values'][$j]['value_index']];
                        //Mage::log('Update Price: '.$attributeData['values'][$j]['pricing_value']);
                        //Mage::log('Option ID: '.$attributeData['values'][$j]['value_index']);
                        //Mage::log('Price Matrix Option: '.$priceMatrix[$attributeData['values'][$j]['value_index']]);
                    }

                    //if($id == 17){   // Check your $id value
                        $attribute = Mage::getModel('catalog/product_type_configurable_attribute')
                        ->setData($attributeData)
                        ->setId($id)
                        ->setStoreId($product->getStoreId())
                        ->setProductId($product->getId())
                        ->save();
                    //}
                }
            }
            Mage::log('Count: '.$count);
        }
        //Mage::log('Associated SKU: '.$associatedProducts[$j]['sku']);  
        Mage::log('Finished');
    }
    
    public function multiArrayValueSearch($haystack, $needle, &$result, &$aryPath=NULL, $currentKey='') {
        if (is_array($haystack)) {
            $count = count($haystack);
            $iterator = 0;
            foreach($haystack as $location => $straw) {
                $iterator++;
                $next = ($iterator == $count)?false:true;
                if (is_array($straw)) $aryPath[$location] = $location;
                $this->multiArrayValueSearch($straw,$needle,$result,$aryPath,$location);
                if (!$next) {
                    unset($aryPath[$currentKey]);
                }

            }
        } else {
            $straw = $haystack;
            if ($straw == $needle) {
                if (!isset($aryPath)) {
                    $strPath = "\$result[$currentKey] = \$needle;";
                } else {
                    $strPath = "\$result['".join("']['",$aryPath)."'][$currentKey] = \$needle;";
                }
                $strPath = "'".$strPath."'";
                eval($strPath);
            }
        }
    }
}

?>
