<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of QuickCart
 *
 * @author andrewprendergast
 */
class ServU_QuickCart_Model_QuickCart extends Mage_Core_Model_Abstract {

    private $_qty;
    private $_cart;
    private $_itemResponse;
    private $_requiredMinimum;
    private $_product;
    private $_fieldId;
    
    public function addToCart(){
        //Get POST data
        $data = array();
        $postData = $this->getData('postdata');
        $number_of_fields = $postData['number_of_fields'];
        for($i = 1; $i <= $number_of_fields; $i++){
            //Strip out special characters from configurable skus
            $data['quickaddsku'.$i] = Mage::helper('quickcart/data')->cleanSKU($postData['quickaddsku'.$i]);
            
            //Get configurable options
            if(!empty($postData['quickaddsku'.$i.'options'])){
                $data['quickaddsku'.$i.'selected_options'] = $postData['quickaddsku'.$i.'options'];
            }
        }
        
        //Add Products to Cart
        $this->_cart = Mage::getSingleton('checkout/cart');
        foreach($data as $field_id => $sku){
            $this->_setFieldId($field_id);
            $this->_setItemResponse('notfound');
            
            //Load product from sku
            if(!empty($sku) && $this->_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku)){
                //Make sure item is enabled
                if($this->_product->getStatus() == 2) {
                    $this->_setItemResponse('<div class="error">This item is no longer available</div>');
                }
                //Check for stock
                elseif(!$this->_product->getStockItem()->getIsInStock()) {
                    $this->_setItemResponse('<div class="error">This item is out of stock</div>');
                }
                //Add item to Cart
                else{
                    //Add simple item to Cart
                    if ($this->_product->type_id == "simple"){
                        $this->_getRequiredMinimum();
                        $this->_cart->addProduct( $this->_product->getId(), $this->_qty);
                        $this->_setItemResponse('itemadded');
                    }
                    //Add configurable item to Cart
                    elseif ($this->_product->type_id == "configurable"){
                        if(!empty($data[$this->_fieldId .'selected_options'])){
                            //Add configurable options
                            $options = $data[$this->_fieldId .'selected_options'];
                            if($this->_addConfig($options) === true){
                                $this->_setItemResponse('itemadded');
                            }
                            else{
                                $data[$this->_fieldId .'options'] = $this->_getFirstAttribute();
                                $this->_setItemResponse('<span class="error">Unable to add requested configuration</span>');
                            }
                        }
                        else{
                            $data[$this->_fieldId .'options'] = $this->_getFirstAttribute();
                        }
                    }
                }
            }
            //Check for partial sku items (ie: jrcq-182 and lavq-7322)
            elseif(strlen($sku) > 5){
                $this->_checkPartialSkus($sku);
            }
            
            $data[$this->_fieldId .'sku'] = $sku;
            $data[$this->_fieldId .'minimum'] = $this->_requiredMinimum;
            $data[$this->_fieldId .'msg'] = $this->_itemResponse;
        }

        $data['msg'] = 'failure';
        if($this->_cart->save()){
            $data['msg'] = 'success';
        }

        //Get current cart total and quantity
        $totals = Mage::getSingleton('checkout/cart')->getQuote()->getTotals();
        $subtotal = $totals["subtotal"]->getValue();
        $data['cartPrice'] = Mage::helper('core')->formatPrice($subtotal, false);
        $data['cartQty'] = Mage::helper('quickcart/data')->showCartQty();

        return $data;
    }
   
    public function getNextAttribute(){
        //Get POST data
        $data = array();
        $postData = $this->getData('postdata');
        //Strip out special characters from configurable skus
        $sku =Mage::helper('quickcart/data')->cleanSKU($postData['sku']);
        $this->_setFieldId($postData['field_id']);
//        $select_id = $postData['select_id'];
        
        $selectedAttributes = array();
        foreach($postData as $postKey => $value){
            if($postKey != 'sku' && $postKey != 'field_id' && $postKey != 'select_id'){
                $selectedAttributes[$postKey] = $value;
            }
        }

        //Load product by SKU and get next configurable attribute
        if($this->_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku)){
            $productAttributes = $this->_product->getTypeInstance(true)->getConfigurableAttributesAsArray($this->_product);
            
            //Get this product's associated products
            $associatedProducts = $this->_product->getTypeInstance()->getUsedProducts();
            
            foreach($selectedAttributes as $selectedKey => $selectedValue){
                //Filter out attributes that have already been selected
                foreach($productAttributes as $product_key => $product_value){
                    $attribute_id = explode('_',$selectedKey);
                    $attribute_id = $attribute_id[1];

                    if($attribute_id == $product_value['attribute_id']){ 
                        unset($productAttributes[$product_key]);
                    }
                }
                
                //Filter out associated products that do not have user's selected configuration
                foreach($associatedProducts as $associatedKey => $associatedValue){
                    //Get attribute id from the select field's id
                    $select_field_id = explode('_',$selectedKey);
                    $attribute_id = $select_field_id[1];

                    //Get this attribute_code from attribute_id
                    if($attribute_code = $this->_getAttributeCode($attribute_id)){
                        if(!empty($associatedProducts[$associatedKey])){
                            if($selectedValue != $associatedProducts[$associatedKey]->$attribute_code){
                                unset($associatedProducts[$associatedKey]);
                            }
                        }
                    }
                }
            }

            //Use remaining attributes to get available options and build dropdown
            $html = '<br/>';
            $html .= $this->_buildOptionsHTML($productAttributes, $associatedProducts);
            $data['next_attribute'] = $html;
        }
         
        return $data;
    }    
    
    private function _getFirstAttribute(){
        $this->_setItemResponse('<span class="error">Please select option(s)</span>');
        
        //Get attributes applicable to the configurable product
        $productAttributes = $this->_product->getTypeInstance(true)->getConfigurableAttributesAsArray($this->_product);
        
        //Get this product's associated products
        $associatedProducts = $this->_product->getTypeInstance()->getUsedProducts();
        
        //Build dropdown
        $firstAttribute = '<div id="' . $this->_fieldId . 'optioncontainer">';
        $firstAttribute .= $this->_buildOptionsHTML($productAttributes, $associatedProducts);
        $firstAttribute .= '</div>';

        return $firstAttribute;
    }

    private function _buildOptionsHTML($productAttributes, $associatedProducts){
        $html = '';
        foreach($productAttributes as $productAttribute){
            $attribute_code = $productAttribute['attribute_code'];
            $attribute_options = array();
            $field_key = $this->_fieldId . '_' . $productAttribute['attribute_id'];

            //Build dropdown
            $html .= '<select class="quick-cart-select" id="'.$field_key.'" >';
            $html .= '<option value="">Select '.$productAttribute['label'].'</option>';
            
            foreach($associatedProducts as $associatedProduct){
                //Mage::log($associatedProduct->sku, null, 'quickcart.log');
                $associatedProductEnabled = Mage::getModel('catalog/product')->loadByAttribute('sku',$associatedProduct->sku)->getStatus();
                $attribute_option = $associatedProduct->$attribute_code;
                
                if($associatedProductEnabled == 1 && !in_array($attribute_option, $attribute_options)){
                    $attribute_options[] = $attribute_option;
                    $attribute_label = $this->_getOptionLabel($attribute_option);
                    $html .= '<option value="'.$attribute_option.'">'.$attribute_label['value'].'</option>';
                }
            }
            
            $html .= '</select>';

            //Only return options for first attribute
            break;
        }
        return $html;
    }
    
    private function _addConfig($options){
        //Format attribute options
        $super_attributes = Mage::helper('quickcart')->formatOptions($options);
        
        //Get required minimum
        $this->_getRequiredMinimum($super_attributes);
            
        //Add to cart
        $params = array(
            //'product' => $this->_product->getId(),
            'super_attribute' => $super_attributes,
            'qty' => $this->_qty,
        );
        if($this->_cart->addProduct($this->_product->getId(), $params)){
            return true;
        }
        return false;
    }
    
    private function _getAttributeCode($value){
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $result = $read->fetchRow("SELECT attribute_code FROM eav_attribute WHERE attribute_id = '".$value."';");
        return $result['attribute_code'];
    }
    
    private function _getOptionLabel($value){
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $result = $read->fetchRow("SELECT value FROM eav_attribute_option_value WHERE option_id = '".$value."';");
        return $result;
    }

    private function _setQuantity($value){
        $this->_qty = $value;
    }

    private function _setRequiredMinimum($value = false){
        $this->_requiredMinimum = $value;
    }

    private function _setItemResponse($value = 'notfound'){
        $this->_itemResponse = $value;
    }
    
    private function _setFieldId($field_id){
        $this->_fieldId = $field_id;
    }
    
    private function _getRequiredMinimum($super_attributes = ''){
        //Prevent product's simple sku from being added to cart
        $checkProduct = $this->_product;
        
        //Use configurable product if attributes are set
        if(!empty($super_attributes)){
            $configProduct = Mage::getModel('catalog/product_type_configurable')
                    ->getProductByAttributes($super_attributes, $this->_product);
            $checkProduct = $configProduct;
        }
        
        $minQty = Mage::getModel("cataloginventory/stock_item")
                ->loadByProduct($checkProduct->getId())
                ->getMinSaleQty();
        
        //Set minimum quantity
        if(!empty($minQty) && $minQty > 1){
            $this->_setQuantity($minQty);
            $this->_setRequiredMinimum(true);
        }
        else{
            $this->_setQuantity(1);
            $this->_setRequiredMinimum(false);
        }
    }  

    private function _checkPartialSkus($sku){
        //Catalog lists some items with * (see jrcq-182*)
        $sku = str_replace('*', '', $sku);
        
        //Load possible skus
        if($partials = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('sku', array('like' => '%'.$sku.'%'))->load()){
            $count = 0;
            //Build list of skus
            $html = '<div id="'. $this->_fieldId .'_partial">';
            foreach($partials as $partial){
                //Do not display configurable items
                if(!preg_match("/\^|\=|\#|\*/",$partial->sku)){
                    //Only show items with available stock
                    if($partial->getStockItem()->getIsInStock() == true ){
                        $html .= '<a href="#quick-add-cart" onclick="addPartialMatches(\''.$partial->sku.'\', \''. $this->_fieldId .'\'); return false;" >'.$partial->sku.'</a><br/>';
                        //Display link to each product's page
                        //$html .= ' (<a href="'.$partial->getProductUrl().'" target="_black">View Product Page</a>)';
                        $count++;
                    }
                }
                //Limit number of results displayed
                if($count > 4){
                    $html .= '<div class="error">More SKUs available. Please use the search bar to find more SKUs.</div>';
                    break;
                }
            }
            $html .= '</span';

            //Return list of skus
            if($count > 0){
                $this->_setItemResponse('<span class="error">Select a SKU</span>' . $html);
            }
        }
    }
}
?>