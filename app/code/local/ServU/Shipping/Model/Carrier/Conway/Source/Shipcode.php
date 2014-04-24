<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Shipcode
 *
 * @author dustinmiller
 */
class ServU_Shipping_Model_Carrier_Conway_Source_Shipcode {
    
    public function toOptionArray()
    {
       return array(
            array(
               'label' => 's',
               'value' => 's'),
            array(
               'label' => 'S',
               'value' => 'S'),
            array(
                'label' => 'c',
                'value' => 'c'),
            array(
                'label' => 'C',
                'value' => 'C'),
           array(
                'label' => '3',
                'value' => '3'),
        );  
    }
}

?>
