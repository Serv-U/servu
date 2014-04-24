<?php

class WP_CustomMenu_Model_Observer
{
    public function observeLayoutHandleInitialization(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfig('custom_menu/general/enabled')) return;
        if (Mage::getStoreConfig('custom_menu/general/ie6_ignore') && Mage::helper('custommenu')->isIE6()) return;
        if (Mage::getStoreConfig('custom_menu/general/menu_position') == WP_CustomMenu_Model_System_Config_Source_Position::POSITION_LEFT)
        {
            $controllerAction = $observer->getEvent()->getAction();
            $controllerAction->getLayout()->getUpdate()->addHandle('wp_custommenu_left');
        }
    }
}
