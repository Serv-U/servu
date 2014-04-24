    <?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Available
 *
 * @author dustinmiller
 */
class ServU_Shipping_Block_Checkout_Onepage_Shipping_Method_Available 
    extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
    {
        
        public function getAccessorialsInfo() {
            return Mage::getSingleton('servu_shipping/shipping')->getAccessorials();
        }
        
        public function getAccessorialsPricing() {
            return Mage::getSingleton('servu_shipping/shipping')->getAccessorialsPricing();
        }
        
    }

?>
