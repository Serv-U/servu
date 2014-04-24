<?php

class Aero_Catalogrequest_Model_Status extends Varien_Object
{
    const STATUS_PENDING	= 1;
    const STATUS_PROCESSED	= 2;

    static public function getOptionArray()
    {

        return array(
            self::STATUS_PENDING    => Mage::helper('catalogrequest')->__('Pending'),
            self::STATUS_PROCESSED   => Mage::helper('catalogrequest')->__('Processed')
        );

    }
}