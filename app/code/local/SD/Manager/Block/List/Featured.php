<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Featured
 *
 * @author dustinmiller
 */
class SD_Manager_Block_List_Featured 
    extends SD_Manager_Block_List_All
{
    /**
     * @return SD_Manager_Model_Mysql4_Manufacturer_Collection
     */
    public function getValuesCollection($randomize = false)
    {
        if (null === $this->_valuesCollection) {
            //the attribute value collection
            $this->_valuesCollection = Mage::getModel('sd_manager/manufacturer')->getCollection();

            $this->_valuesCollection
                ->addStoreFilter(Mage::app()->getStore()->getId(), true)
                ->addAttributeCodeFilter($this->getAttributeCode())
                ->addFeaturedFilter()
                ->addEnabledFilter()
                ->addAttributeToSort('sort_order', 'ASC');

            if ($randomize) {
                $this->_valuesCollection->randomize();
            }
        }
        return $this->_valuesCollection;
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }
}