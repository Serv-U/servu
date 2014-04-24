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
 * ShoppingFeeds feed import model
 *
 * @category    ShoppingFeeds
 * @package     ShoppingFeeds_Feed
 */
class ShoppingFeeds_Feed_Model_Thefindimport extends ShoppingFeeds_Feed_Model_Import
{
    const XML_NODE_FIND_FEED_ATTRIBUTES = 'shoppingfeeds_feed_find_attributes';

    /**
     * @desc Return TheFind's feed settings
     * @param string $value
     * @return string
     */
    protected function getFeedSettings($value){
        switch ($value) {
            case 'ftp_server':
                return 'thefindfeed/settings/thefindftp_server';
                break;
            case 'ftp_user':
                return 'thefindfeed/settings/thefindftp_user';
                break;
            case 'ftp_password':
                return 'thefindfeed/settings/thefindftp_password';
                break;
            case 'ftp_path':
                return 'thefindfeed/settings/thefindftp_path';
                break;
            case 'feed_filename':
                return 'thefindfeed/settings/thefindfeed_filename';
                break;
        }
    }
    
    /**
     * List import codes (attribute map) model
     *
     * @return mixed
     */
    protected function _getImportAttributes()
    {
        $attributes = Mage::getResourceModel('shoppingfeeds_feed/codes_thefindcollection')
          ->getImportAttributes();

        if (!Mage::helper('shoppingfeeds_feed/thefind')->checkRequired($attributes)) {
            return false;
        }
        return $attributes;
    }
}
