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
class SD_Dropship_Model_Mysql4_Supplier 
    extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('sd_dropship/supplier', 'id');
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
			$select = $read->select()
				->from(array('main_table' => $this->getMainTable()))
				->joinLeft( array('aov' => $this->getTable('eav/attribute_option_value')), 'aov.option_id = main_table.attribute_option_id AND aov.store_id = main_table.attribute_value_store_id', array('value_id', 'value', 'store_id', 'option_id'))
				->join(     array('ao'  => $this->getTable('eav/attribute_option')),       'ao.option_id = aov.option_id', array())
				->join(     array('a'   => $this->getTable('eav/attribute')),              'a.attribute_id = ao.attribute_id', array('attribute_code', 'frontend_label'))
				->where('main_table.id = ?', $value)
            	->where('SUBSTRING(aov.value,1,1) != \'-\'');

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);

        return $this;

    	/*if (strcmp($value, (int)$value) !== 0) {
            $field = 'url_key';
        }
        return parent::load($object, $value, $field);*/
    }

    public function loadFromAttribute(Mage_Core_Model_Abstract $object, $attribute_code = null, $option_id = null, $store_id = null)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($attribute_code)) {
			$select = $read->select()
				->from(array('main_table' => $this->getMainTable()))
				->joinRight(array('aov' => $this->getTable('eav/attribute_option_value')), 'aov.option_id = main_table.attribute_option_id AND aov.store_id = main_table.attribute_value_store_id', array('value_id', 'value', 'store_id', 'option_id'))
				->join(     array('ao'  => $this->getTable('eav/attribute_option')),       'ao.option_id = aov.option_id', array())
				->join(     array('a'   => $this->getTable('eav/attribute')),              'a.attribute_id = ao.attribute_id', array('attribute_code', 'frontend_label'))

				->where('a.attribute_code = ?', $attribute_code)
				->where('ao.option_id = ?', $option_id)
				->where('aov.store_id in (0, ?)', $store_id)
				->order('aov.store_id DESC')
				;

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);

                //set the default values for the new info page, extracted from the attribute values
                if(!$object->getData('name'))         $object->setData('name', __('%s Products', $object->getValue()));
            }
        }

        $this->_afterLoad($object);

        return $this;
    }

    public function getAllowedAttributes() {
        $allowedAttributes = explode(',', Mage::getStoreConfig('sd_manager/attributes/selectedattributes'));

    	$select = $this->_getReadAdapter()->select()
        	->from($this->getTable('eav/attribute'), array('attribute_code','frontend_label'))
        	->where('attribute_code IN (?)', $allowedAttributes)
        	;
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Try to match an identifier which is not saved
     * return option id if page exists
     *
     * @param   string $identifier
     * @param   int $storeId
     * @return  int
     */
    public function getOptionIdFromIdentifier($attribute_code, $identifier, $storeId)
    {
    	/**
    	 * makes a search in the database for values which are simial to the identifier
    	 * first remove all chars which may be accents in the identifier, and replace them with % for the mysql like comparison
    	 */
        $subst = array(
            '-' => '%', 'a'=>'%', 'c'=>'%', 'd'=>'%', 'e'=>'%', 'i'=>'%', 'n'=>'%', 'o'=>'%', 's'=>'%', 'u'=>'%', 'y'=>'%', 'z'=>'%', 'g'=>'%', 'l'=>'%', 'r'=>'%', 't'=>'%',
        );

        // Replace
        $string = strtr($identifier, $subst);

        while (strpos($string, '%%') !== false) {
        	$string = str_replace('%%', '%', $string);
        }

        $select = $this->_getReadAdapter()->select()
				->from(array('aov' => $this->getTable('eav/attribute_option_value')), array('value'))
				->join(array('ao'  => $this->getTable('eav/attribute_option')), 'ao.option_id = aov.option_id', array('option_id'))
				->join(array('a'   => $this->getTable('eav/attribute')), 'a.attribute_id = ao.attribute_id', array())

				->where('a.attribute_code = ?', $attribute_code)
				->where('aov.value LIKE ?', $string)
				->where('aov.store_id in (0, ?)', $storeId)
				->order('aov.store_id DESC')
				;

        $stmt = $this->_getReadAdapter()->query($select, array());
        //the best stripping function is the provided one in php,
        //so we format all the values that match the identifier,
        //and return the option_id for the one that matches
        while ($result = $stmt->fetch()) {
        	if ($identifier == $this->formatUrlKey($result['value'])) {
        		return $result['option_id'];
        	}
        }

        return false;
    }

}

?>
