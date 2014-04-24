<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Order
 *
 * @author dustinmiller
 */
class ServU_Shipping_Block_Adminhtml_Sales_Order_View_Tab_Info 
    extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Info {
    
    public function getCustomVars(){
        $model = Mage::getModel('servu_shipping/miscinformation_order');
        $data = $model->getByOrder($this->getOrder()->getId());
        $confirmationNumbers = array();
        $accessorials = array();
        $customVars = array();
        foreach($data as $key => $value){
            $key = preg_replace("([-0-9])", "", $key);
            if($key === 'confirmation_number') {
                $confirmationNumbers[] = $value;
            }  
            else {
                $accessorials[] = $value;
            }
        }
        
        $customVars['accessorials'] = $accessorials;
        $customVars['confirmation_numbers'] = $confirmationNumbers;
        
        return $customVars;  
    }
}

?>
