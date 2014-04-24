<?php

class WP_CustomMenu_Block_Toggle extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        if (!Mage::getStoreConfig('custom_menu/general/enabled')) return;
        if (Mage::getStoreConfig('custom_menu/general/ie6_ignore') && Mage::helper('custommenu')->isIE6()) return;
        if (Mage::getStoreConfig('custom_menu/general/menu_position') == WP_CustomMenu_Model_System_Config_Source_Position::POSITION_TOP)
        {
            $layout = $this->getLayout();
            $topnav = $layout->getBlock('catalog.topnav');
            if (is_object($topnav))
            {
                $topnav->setTemplate('webandpeople/custommenu/top.phtml');
                $head = $layout->getBlock('head');
                $head->addItem('skin_js', 'js/webandpeople/custommenu/custommenu.js');
                $head->addItem('skin_css', 'css/webandpeople/custommenu/custommenu.css');
            }
        }
        elseif (Mage::getStoreConfig('custom_menu/general/menu_position') == WP_CustomMenu_Model_System_Config_Source_Position::POSITION_LEFT
                && Mage::getStoreConfig('custom_menu/general/top_static_block'))
        {
            $layout = $this->getLayout();
            $topnav = $layout->getBlock('catalog.topnav');
            if (is_object($topnav))
            {
                $topnav->setTemplate('webandpeople/custommenu/top-static-block.phtml');
            }
        }
    }
}
