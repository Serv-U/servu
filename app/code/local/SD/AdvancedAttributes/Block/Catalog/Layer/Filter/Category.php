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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog layer category filter
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SD_AdvancedAttributes_Block_Catalog_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category
{
    public function __construct() {
        parent::__construct();
        $this->setTemplate('advancedattributes/catalog/layer/category/check.phtml');
    }
    
    public function getChildrenCats($catId) {
        $category = Mage::getModel('Mage_Catalog_Model_Category')->load($catId); 
        $subCats = $category->getChildren();
        $catArray = explode(',', $subCats);
        $subCatActive = false;

        if (count($catArray) == 0 || $catArray[0] == ''
                || !$this->isSubcategoriesContainProducts($category)) {
            return '';
        }
        
        $_filters = Mage::getSingleton('Mage_Catalog_Block_Layer_State')->getActiveFilters();

        $activeFilters = array();
        foreach ($_filters as $_filter) {
            $valueArray = explode(',',$_filter->getValueString());
            foreach ($valueArray as $value) {
                $activeFilters[] = $value;
            }
        }
        
        $innerHtml = '';
        
        foreach ($catArray as $cat) {
            $checked = '';
            if (in_array($cat, $activeFilters)) { 
                $subCatActive = true;
                $checked = 'checked="checked"';
                $innerHtml.="<li class='active'>";
            } else {
                $innerHtml.= "<li>";
            }
            $subCategory = Mage::getModel('catalog/category')->load($cat);
            $prodCollection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($subCategory);
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($prodCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($prodCollection);
            $innerHtml .= "<input type='checkbox' value='cat[]=".$subCategory->getId()."' ".$checked."><label>".$subCategory->getName()." (".$prodCollection->count().")</label></li>";
            $innerHtml .= "";
        }
        
        
        if (in_array($catId,$activeFilters) || $subCatActive) {
            $html = "<ol class='subcat-list cat-".$catId."'>";
        } else {
            $html = "<ol style='display:none' class='subcat-list cat-".$catId."'>";
        }
        $html .= $innerHtml;
        $html .= "</ol>";
        return $html;
    }
    
    /**
    * Check if category or it's subcategories contains at least one product.
    *
    * @param $category
    * @return bool
    */
    protected function isSubcategoriesContainProducts($category) {
        $subs = $category->getChildren();
        $subcatArray = explode(',', $subs);
        foreach ($subcatArray as $cat) {
            if (Mage::getModel('catalog/category')->load($cat)->getProductCount() > 0) {
                return true;
            }
        }
        return false;
    }
    
    public function canBeShown() {
        return $this->getItemsCount() > 0;
    }
}
