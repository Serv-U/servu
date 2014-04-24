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
 * ShoppingFeeds feed attribute map grid container
 *
 * @category    ShoppingFeeds
 * @package     ShoppingFeeds_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ShoppingFeeds_Feed_Block_Adminhtml_Thefind_List_Codes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize grid container settings
     *
     */
    public function __construct()
    {
        Mage::getModel('shoppingfeeds_feed/thefindimport')->dispatch();
        
        $this->_blockGroup      = 'shoppingfeeds_feed';
        $this->_controller      = 'adminhtml_thefind_list_codes';
            $this->_headerText      = Mage::helper('shoppingfeeds_feed')->__('Manage Attributes for TheFind\'s Feed');
        $this->_addButtonLabel  = Mage::helper('shoppingfeeds_feed')->__('Add new');

        parent::__construct();

        $url = $this->getUrl('*/thefind_codes_grid/editForm');
        $this->_updateButton('add', 'onclick', 'openNewImportWindow(\''.$url.'\');');
    }
}
