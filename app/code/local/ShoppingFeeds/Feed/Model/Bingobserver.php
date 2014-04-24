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
 * ShoppingFeeds feed main observer 
 *
 * @category    ShoppingFeeds
 * @package     ShoppingFeeds_Feed
 */
class ShoppingFeeds_Feed_Model_Bingobserver extends ShoppingFeeds_Feed_Model_Observer
{
    /**
     * Save system config event 
     *
     * @param Varien_Object $observer
     */
    public function saveSystemConfig($observer)
    {
        $store = $observer->getStore();
        $website = $observer->getWebsite();
        $groups['settings']['fields']['bingcron_schedule']['value'] = $this->_getSchedule();

        Mage::getModel('adminhtml/config_data')
                ->setSection('bingfeed')
                ->setWebsite($website)
                ->setStore($store)
                ->setGroups($groups)
                ->save();
    }
    
    protected function _getFrequency($data){
        return !empty($data['settings']['fields']['bingcron_frequency']['value'])?
                         $data['settings']['fields']['bingcron_frequency']['value']:
                         0;
    }
    
    protected function _getHours($data){
        return !empty($data['settings']['fields']['bingcron_hours']['value'])?
                         $data['settings']['fields']['bingcron_hours']['value']:
                         0;
    }
    
    protected function _getMinutes($data){
        return !empty($data['settings']['fields']['bingcron_minutes']['value'])?
                         $data['settings']['fields']['bingcron_minutes']['value']:
                         0;
    }
}