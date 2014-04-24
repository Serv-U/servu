<?php
class SD_AdvancedAttributes_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category {
    /**
	 * Apply filters from the request
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @param $filterBlock
	 * @return SD_AdvancedAttributes_Model_Catalog_Layer_Filter_Category
	 */
	public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
		$filter = $request->getParam($this->getRequestVar());
                
		if (!is_array($filter)) {
			return $this;
                }
                
		$this->_categoryId = null;

		Mage::register('current_category_filter', $this->getCategory(), true);

		/** @var $categoryResource Mage_Catalog_Model_Resource_Category */
		$categoryResource = Mage::getResourceModel('catalog/category');
		$filter = $categoryResource->verifyIds($filter);
		$filter = $this->filterCategoriesByItsParent($filter, $this->getCategory());

		if (count($filter) == 0) {
			return $this;
		}

		$this->updateCategoryFilter($this->getLayer()->getProductCollection(), $filter);

		foreach ($filter as $categoryId) {
                        $this->getLayer()->getState()->addFilter(
                            $this->_createItem(Mage::getModel('catalog/category')->load($categoryId)->getName(), $filter)
                        );
		}

		return $this;
	}
        
        /**
	 * Add filters to the collection.
	 * @param SD_AdvancedAttributes_Model_Mysql4_Product_Collection $collection
	 * @param $categories
	 */
	protected function updateCategoryFilter(SD_AdvancedAttributes_Model_Mysql4_Product_Collection $collection, $categories) {
		$collection->addCategoriesFilter($categories);
	}
        
        /**
	 * Returns list of subcategories recursively.
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @return mixed
	 */
	protected function getSubcategories(Mage_Catalog_Model_Category $category) {
		if (!isset($this->subcategories[$category->getId()])) {
			$list = array();
			$categories = $category->getChildrenCategories();
			$this->getAllChildCategories($categories, $list);
			$this->subcategories[$category->getId()] = $list;
		}

		return $this->subcategories[$category->getId()];
	}
        
        /**
	 * Adds child categories of the current roots to the array and recursively
	 * execute this action on these child categories.
	 *
	 * @param $roots
	 * @param $array
	 */
	protected function getAllChildCategories($roots, &$array) {
		/** @var $root Mage_Catalog_Model_Category */
		foreach($roots as $root) {
			$array[] = $root;
			$childrenCategories = $root->getChildrenCategories();
			$root->setLoadedChildrenCategories($childrenCategories);
			if (count($childrenCategories) > 0) {
				$this->getAllChildCategories($childrenCategories, $array);
			}
		}
	}
        
        /**
	 * Method used to don't allow apply category filters that doesn't
	 * belong to the current category subcategories.
	 *
	 * @param array $categories
	 * @param Mage_Catalog_Model_Category $parent
	 * @return array
	 */
	protected function filterCategoriesByItsParent(array $categories, Mage_Catalog_Model_Category $parent) {
		$trueCategories = $this->getSubcategories($parent);
		$trueCategoryIds = array();
		foreach ($trueCategories as $trueCategory) {
			$trueCategoryIds[$trueCategory->getId()] = $trueCategory;
		}

		foreach ($categories as $key => $categoryIdToVerify) {
			if (!isset($trueCategoryIds[$categoryIdToVerify])) {
				unset($categories[$key]);
			}
		}

		return $categories;
	}
        
        public function updateStateItemsStatus() {
		$helper = Mage::helper('advancedattributes');
		$helper->initFilterItems($this->getLayer()->getState(), $this->_items);
	}

	protected $subcategories = array();

}