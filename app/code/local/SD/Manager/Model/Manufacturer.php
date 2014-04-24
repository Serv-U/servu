<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manufacturer
 *
 * @author dustinmiller
 */
class SD_Manager_Model_Manufacturer 
    extends Mage_Core_Model_Abstract
{

    const NOROUTE_PAGE_ID = 'no-route';
    protected $_eventPrefix = 'sd_manager';

    protected function _construct()
    {
        $this->_init('sd_manager/manufacturer');
    }

    public function load($id, $field=null)
    { 
        if (is_null($id)) {
            return $this->noRoutePage();
        }
        return parent::load($id, $field);
    }

    
    public function loadFromAttribute($attribute_code, $option_id, $store_id)
    {
        $this->_getResource()->loadFromAttribute($this, $attribute_code, $option_id, $store_id);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
    /**
     * Check if page identifier exist for specific store
     * return manufacturer information id if page exists
     *
     * @param   string $identifier
     * @param   int $storeId
     * @return  int
     */
    public function checkIdentifierInPages($attribute_code, $identifier, $storeId)
    {
        return $this->_getResource()->checkUrlKeyInPages($attribute_code, $identifier, $storeId);
    }

    /**
     * Check if page identifier exist for specific store
     * return manufacturer information id if page exists
     *
     * @param   string $identifier
     * @param   int $storeId
     * @return  int
     */
    public function getOptionIdFromIdentifier($attribute_code, $identifier, $storeId)
    {
        return $this->_getResource()->getOptionIdFromIdentifier($attribute_code, $identifier, $storeId);
    }

    public function getImageUrl($field = 'logo')
    {
        $url = false;
        if ($image = ('logo' == $field ? $this->getLogo() : $this->getLogo())) {
            $url = Mage::getBaseUrl('media').'catalog/manufacturers/'.$image;
        }
        return $url;
    }

}

?>