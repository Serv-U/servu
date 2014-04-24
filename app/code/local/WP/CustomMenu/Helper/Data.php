<?php

class WP_CustomMenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isIE6()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/\bmsie [1-6]/i', $_SERVER['HTTP_USER_AGENT']);
    }
}
