<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    
 * @package     _storage
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * TheFind feed main observer 
 *
 * @category    Find
 * @package     Find_Feed
 */
class ShoppingFeeds_Feed_Model_Observer {
    /**
     * Transform system settings option to cron schedule string
     * 
     * @return string
     */
    protected function _getSchedule() {
        $data = Mage::app()->getRequest()->getPost('groups');
        
        $frequency = $this->_getFrequency($data);
        
        $hours = $this->_getHours($data);
        $minutes = $this->_getMinutes($data);

        switch ($frequency) {
            case ShoppingFeeds_Feed_Model_Adminhtml_System_Source_Cron_Frequency::DISABLED:
            default:
                //Set to date that does not exist
                $schedule = "$minutes $hours 31 2 0";
                break;
            case ShoppingFeeds_Feed_Model_Adminhtml_System_Source_Cron_Frequency::DAILY:
                $schedule = "$minutes $hours * * *";
                break;
            case ShoppingFeeds_Feed_Model_Adminhtml_System_Source_Cron_Frequency::WEEKLY:
                $schedule = "$minutes $hours * * 1";
                break;
            case ShoppingFeeds_Feed_Model_Adminhtml_System_Source_Cron_Frequency::MONTHLY:
                $schedule = "$minutes $hours 1 * *";
                break;
            case ShoppingFeeds_Feed_Model_Adminhtml_System_Source_Cron_Frequency::BI_WEEKLY:
                $schedule = "$minutes $hours * * 1,5";
                break;
//            case ShoppingFeeds_Feed_Model_Adminhtml_System_Source_Cron_Frequency::EVERY_MINUTE:
//                $schedule = "0-59 * * * *"; 
//                break;
        }

        return $schedule;
    }
}