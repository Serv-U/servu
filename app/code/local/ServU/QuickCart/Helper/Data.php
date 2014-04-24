<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Data
 *
 * @author andrewprendergast
 */
class ServU_QuickCart_Helper_Data extends Mage_Core_Helper_Abstract {

    public function formatOptions($options){
        $selection = array();
        $option = explode(';',$options);
        $i = 0;
        $super_attributes = array();

        while(!empty($option[$i])){
            $selection = explode(':',$option[$i]);
            $attribute_id = $selection[0];
            $attribute_value = $selection[1];
            if(empty($attribute_value) || empty($attribute_id)){
                return false;
            }
            $super_attributes[$attribute_id] = $attribute_value;
            $i++;
        }

        return $super_attributes;
    }    
    
    public function showCartQty(){
        $cartQty = Mage::helper('checkout/cart')->getSummaryCount();
        
        if($cartQty > 1){
            return $cartQty." items";
        }
        elseif($cartQty == 1){ 
            return $cartQty." item";
        }
        else{
            return '0 items';
        }
    }
    
    public function cleanSKU($strSku){
        //Trim whitespaces
        $strSku = trim($strSku, ' ');

        //Strip special characters from configurable skus
        if(preg_match("/\^|\=|\#|\*/", $strSku)){
            $chars = array("#","*","^","=");
            foreach($chars as $char){
                if(strpos($strSku, $char) !== false){
                    $strSku = substr($strSku, 0, strpos($strSku, $char));
                }
            }
        }
        
        return $strSku;
    }
}

?>
