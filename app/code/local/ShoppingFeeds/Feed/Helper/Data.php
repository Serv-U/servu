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
 * ShoppingFeeds Feed helper
 *
 * @category   ShoppingFeeds
 * @package    ShoppingFeeds_Feed
 */
class ShoppingFeeds_Feed_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Product entity type
     *
     * @return int
     */
    public function getProductEntityType()
    {
        return Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();
    }
    
     /**
     * @desc Format feed's fields to meet Bing and TheFind requirements
     * @param array $attributesRow
     * @param string $key
     * @return string
     */
    public function formatFeedFields($str, $value){
        //Format missing images to meet requirements
        if($value == 'image'){
            if($str == 'no_selection' || $str == ''){
                //Formatting for missing image items for TheFind (Bing does not accept items without images)
                //$str = 'No Image';
                
                //Use 'Product Image Not Available' image if product does not have an image...
                $str = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product/cache/2/image/265x/9df78eab33525d08d6e5fb8d27136e95/placeholder/websites/2/noimage2-su.jpg';
            } else {
                $str = ltrim($str, '/');
                $str = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product/' . $str;
            }
        }
        //Strip html, returns, and new lines from descriptions
        elseif($value == 'description'){
            $str = strip_tags($str);
            $str = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $str);
        }
        //Format price
        elseif($value == 'price'){
            $str = round($str,2);
            $str = number_format($str,2);
        }
        //Format url
        elseif($value == 'url_path'){
            $str = Mage::getBaseUrl() . $str;
        }

        $str = trim($str, ' ');

        return $str;
    }
}