<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FreightClass
 *
 * @author dustinmiller
 */
class ServU_Shipping_Model_Carrier_Conway_Source_Freightclass {

    public function toOptionArray()
    {
        return array(
            array(
               'label' => '85',
               'value' => '85'),
            array(
               'label' => '100',
               'value' => '100'),
            array(
                'label' => '125',
                'value' => '125'),
            array(
                'label' => '150',
                'value' => '150'),
           array(
                'label' => '175',
                'value' => '175'),
            array(
                'label' => '250',
                'value' => '250'),
        );
    }
}

?>