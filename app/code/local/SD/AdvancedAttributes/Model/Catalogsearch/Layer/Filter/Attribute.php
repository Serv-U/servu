<?php
class SD_AdvancedAttributes_Model_Catalogsearch_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    /*public function getImage() {
        //$attribute = $this->getAttributeModel();
        //Mage::log($attribute->getId());
        //$model = Mage::getModel('advancedattributes/advancedattributes')->loadFromAttributeId($attribute->getId());
        /*$group_id = $attribute->getData('attribute_set_info/' . $setId . '/group_id');
        $group = Mage::getModel('eav/entity_attribute_group')->load($group_id);
        $group_name = $group->getData('attribute_group_name');

        return 'images';
    }*/
    
    protected function _initItems()
    {
        $data = $this->_getItemsData();
        $items=array();
        foreach ($data as $itemData) {
            $items[] = $this->_createExtendedItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['image'],
                $itemData['count']
            );
        }
        $this->_items = $items;
        return $this;
    }
    
    
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $key = $this->getLayer()->getStateKey().'_'.$this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $options = $attribute->getFrontend()->getSelectOptions();
            $optionsCount = $this->_getResource()->getCount($this);
            $data = array();
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }
                if (Mage::helper('core/string')->strlen($option['value'])) {
                    // Check filter type
                    $advancedOptionsModel = Mage::getSingleton('advancedattributes/options')->loadFromOptionId($option['value']);
                    
                    $imageUrl = '';
                    
                    if($advancedOptionsModel->getLayeredImage() != '') {
                        $imageUrl = $advancedOptionsModel->getLayeredImage();
                    }
                    
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']])) {
                            $data[] = array(
                                'label' => $option['label'],
                                'value' => $option['value'],
                                'count' => $optionsCount[$option['value']],
                                'image' => $imageUrl
                            );
                        }
                    }
                    else {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $option['value'],
                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                            'image' => $imageUrl
                        );
                    }
                }
            }

            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG.':'.$attribute->getId()
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }
    
    protected function _createExtendedItem($label, $value, $image, $count=0)
    {
        return Mage::getModel('catalog/layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setImage($image);
    }
    
    protected function _getResource() {
        if (is_null($this->_resource)) {
                $this->_resource = Mage::getResourceModel('advancedattributes/layer_multiFilter_attribute');
        }
        return $this->_resource;
    }
    
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
        $filter = $request->getParam($this->_requestVar);

        if (!is_array($filter)) {
            $text = $this->_getOptionText($filter);
            if ($filter && strlen($text)) {
                $this->_getResource()->applyFilterToCollection($this, $filter);
                $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
                $this->_items = array();
            }
            return $this;
        }

        $addToFilter = array();
        foreach($filter as $filterValue) {
                $text = $this->_getOptionText($filterValue);
                if ($text) {
                    $this->getLayer()->getState()->addFilter($this->_createItem($text, $filterValue));
                    $addToFilter[] = $filterValue;
                }
        }

        if (count($addToFilter) > 0) {
                $this->_getResource()->applyFilterToCollection($this, $addToFilter);
        }

        return $this;
    }

}