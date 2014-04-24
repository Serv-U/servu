<?php
/**
 * Magento
 *
 */

/**
 * Category edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Openstepmedia_AdminCategoryProducts_Adminhtml_Block_Catalog_Category_Edit_Form extends Mage_Adminhtml_Block_Catalog_Category_Edit_Form
{
    public function getHeader()
    {
        if ($this->hasStoreRootCategory()) {
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                $categoryUrl = $category->getUrl();

                $header = "<a href='" . $categoryUrl . "' target='_blank'>" . $this->htmlEscape($this->getCategoryName()) . ($this->getCategoryId() ? ' (' . Mage::helper('catalog')->__('ID: %s', $this->getCategoryId()) . ')' : '') . "</a>";
                return $header;
            } else {
                $parentId = (int) $this->getRequest()->getParam('parent');
                if ($parentId && ($parentId != Mage_Catalog_Model_Category::TREE_ROOT_ID)) {
                    return Mage::helper('catalog')->__('New Subcategory');
                } else {
                    return Mage::helper('catalog')->__('New Root Category');
                }
            }
        }
        return Mage::helper('catalog')->__('Set Root Category for Store');
    }
}
