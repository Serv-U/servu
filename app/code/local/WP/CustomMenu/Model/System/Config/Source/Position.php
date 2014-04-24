<?php

class WP_CustomMenu_Model_System_Config_Source_Position
{
    const POSITION_TOP  = 0;
    const POSITION_LEFT = 1;

    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::POSITION_TOP,
                'label' => Mage::helper('custommenu')->__('Top Navigation'),
            ),
            array(
                'value' => self::POSITION_LEFT,
                'label' => Mage::helper('custommenu')->__('Left Sidebar'),
            ),
        );
    }
}
