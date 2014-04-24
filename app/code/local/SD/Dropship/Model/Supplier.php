<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Supplier
 *
 * @author dustinmiller
 */
class SD_Dropship_Model_Supplier 
    extends Mage_Core_Model_Abstract
{

    const NOROUTE_PAGE_ID = 'no-route';
    protected $_eventPrefix = 'sd_dropship';

    protected function _construct()
    {
        $this->_init('sd_dropship/supplier');
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

}

?>
