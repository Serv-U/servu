<?php
class SD_AdvancedAttributes_Model_Catalog_Layer_Filter_Price_Range 
    extends Mage_Catalog_Model_Layer_Filter_Price
{
    protected function _getResource() {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('advancedattributes/layer_filter_price_range');
        }
        return $this->_resource;
    }

    /**
     * Apply filters from request to the collection.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param $filterBlock
     */
    /*public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
        $filter = $request->getParam($this->getRequestVar());

        if (!is_array($filter)) {
            return $this;
        }

        if (isset($filter['0']) && $filter['0'] != '') {
            $this->setAppliedPriceMin((int) $filter['0']);
        }

        if (isset($filter['1']) && $filter['1'] != '') {
            $this->setAppliedPriceMax((int) $filter['1']);
        }
     
        if($this->getAppliedPriceMin() !== null) {
            $minimumPrice = $this->getAppliedPriceMin();
        }
        else {
            $minimumPrice = $this->getMinPriceInt();
        }

        if($this->getAppliedPriceMax() !== null) {
            $maximumPrice = $this->getAppliedPriceMax();
        }
        else {
            $maximumPrice = $this->getMaxPriceInt();
        }

        if($this->getAppliedPriceMax() !== null && $this->getAppliedPriceMin() !== null) {
            $this->addStateItem('$'.floor($minimumPrice).'.00 - $'.ceil($maximumPrice).'.00', $this->getAppliedPriceMin())
                ->setRequestVarKey('0');
        } else if ($this->getAppliedPriceMax() !== null) {
            $this->addStateItem('$'.floor($minimumPrice).'.00 - $'.ceil($maximumPrice).'.00', $this->getAppliedPriceMax())
                ->setRequestVarKey('1');
        } else if ($this->getAppliedPriceMin() !== null) {
            $this->addStateItem('$'.floor($minimumPrice).'.00 - $'.ceil($maximumPrice).'.00', $this->getAppliedPriceMin())
                ->setRequestVarKey('0');
        }

        if ($this->getAppliedPriceMax() || $this->getAppliedPriceMin()) {         
            $this->_getResource()->applyFilterRangeToCollection($this);
        }

        return $this;
    }*/
    
    
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {

        $filter = $request->getParam($this->getRequestVar());
        if (!is_array($filter)) {
                return $this;
        }

        if (isset($filter['0']) && $filter['0'] != '') {
                $this->setAppliedPriceMin((int) $filter['0']);
        }

        if (isset($filter['1']) && $filter['1'] != '') {
                $this->setAppliedPriceMax((int) $filter['1']);
        }

        /*if($this->getAppliedPriceMin() !== null) {
            $minimumPrice = $this->getAppliedPriceMin();
        }
        else {
            $minimumPrice = $this->getMinPriceInt();
        }

        if($this->getAppliedPriceMax() !== null) {
            $maximumPrice = $this->getAppliedPriceMax();
        }
        else {
            $maximumPrice = $this->getMaxPriceInt();
        }*/

        /*if ($this->getAppliedPriceMin() !== null) {
                $this->addStateItem('Low - $'.floor($this->getAppliedPriceMin()).'.00', $this->getAppliedPriceMin())
                    ->setRequestVarKey('0');
        }

        if ($this->getAppliedPriceMax() !== null) {
                $this->addStateItem('High - $'.ceil($this->getAppliedPriceMax()).'.00', $this->getAppliedPriceMax())
                    ->setRequestVarKey('1');
        }*/
        
        if($this->getAppliedPriceMax() !== null && $this->getAppliedPriceMin() !== null) {
            $this->addStateItem('$'.floor($this->getAppliedPriceMin()).'.00 - $'.ceil($this->getAppliedPriceMax()).'.00', $this->getAppliedPriceMin())
                ->setRequestVarKey('0');
        } else if ($this->getAppliedPriceMax() !== null) {
            $this->addStateItem('$'.ceil($this->getAppliedPriceMax()), $this->getAppliedPriceMax())
                ->setRequestVarKey('1');
        } else if ($this->getAppliedPriceMin() !== null) {
            $this->addStateItem('$'.floor($this->getAppliedPriceMin()), $this->getAppliedPriceMin())
                ->setRequestVarKey('0');
        }

        if ($this->getAppliedPriceMax() || $this->getAppliedPriceMin()) {
                $this->_getResource()->applyFilterRangeToCollection($this);
        }

        return $this;
    }

    /**
     * Add item to the state
     *
     * @param $label
     * @param $value
     * @return Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function addStateItem($label, $value) {
        $stateItem = $this->_createItem($label, $value);
        $this->stateItems[] = $stateItem;

        $this->getLayer()->getState()->addFilter($stateItem);
        return $stateItem;
    }

    /**
     * Returns minimal price of the current collection products.
     *
     * @return mixed|string
     */
    public function getMinPriceInt() {
        $minPrice = $this->getData('min_price_int');
        if (is_null($minPrice)) {
                $minPrice = $this->_getResource()->getMinPrice($this);
                $this->setData('min_price_int', $minPrice);
        }

        return $minPrice;
    }

    /**
     * Returns maximal price of the current collection products.
     *
     * @return mixed|string
     */
    public function getMaxPriceInt() {   
        $maxPrice = $this->getData('max_price_int');
        if (is_null($maxPrice)) {
                $maxPrice = $this->_getResource()->getMaxPrice($this);
                $this->setData('max_price_int', $maxPrice);
        }

        return $maxPrice;
    }

    /**
     * Check conditions to show this filter.
     *
     * @return bool
     */
    public function canBeShown() {
        if (abs($this->getMaxPriceInt() - $this->getMinPriceInt()) <= 0.001) {
                return false;
        }

        if ($this->getAppliedPriceMax() && $this->getAppliedPriceMax() < $this->getMinPriceInt()) {
                return false;
        }

        if ($this->getAppliedPriceMin() && $this->getMaxPriceInt() < $this->getAppliedPriceMin()) {
                return false;
        }

        if ($this->_getResource()->getProductsCount($this) <= 1) {
                return false;
        }

        return true;
    }

    /**
     * Applied filter items should be marked to be shown as checked checkbox
     * otherwise they will be passed to the browser as hidden inputs.
     */
    public function updateStateItemsStatus() {
        if ($this->canBeShown()) {
            foreach ($this->stateItems as $stateItem) {
                $stateItem->setOutputInCheckbox(true);
            }
        }
    }

    protected $stateItems  = array();
}