<?php

if (!Mage::getStoreConfig('custom_menu/general/enabled') ||
    (Mage::getStoreConfig('custom_menu/general/menu_position') == WP_CustomMenu_Model_System_Config_Source_Position::POSITION_LEFT
        && !Mage::getStoreConfig('custom_menu/general/top_static_block')))
{
    class WP_CustomMenu_Block_Topmenu extends Mage_Page_Block_Html_Topmenu
    {

    }
    return;
}

class WP_CustomMenu_Block_Topmenu extends WP_CustomMenu_Block_Navigation
{

}
