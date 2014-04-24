<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of advancedattributes
 *
 * @author dustinmiller
 */
class SD_AdvancedAttributes_Model_Mysql4_ConfigurableOptions extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {    
        $this->_init('advancedattributes/configurableOptions', 'id');
    }
    
    /*protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        //process images upload
        $optionID = $object->getData('option_id');
        $image = $object->getData('product_view_image_'.$optionID);

        $path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'attributes' . DS . 'configurables'. DS .'view' . DS;

        if (is_array($image) && !empty($image['delete'])) {
	            //remove the file
	            unlink(Mage::getBaseDir('media') . DS . $image['value']);
	            $object->setData('product_view_image_'.$optionID, '');
        } else {
            
        	$uploaded = false;
        	try {
                    //try to make the upload
	            $uploader = new Varien_File_Uploader('product_view_image_'.$optionID);
	            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	            $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
	            $uploader->save($path);
	            if (($uploaded = $uploader->getUploadedFileName()) > '') {
	            	$object->setData('product_view_image_'.$optionID, $uploaded);
	            }
                    //Mage::log($object->getData('product_view_image_'.$optionID));
                    //Mage::log($object->getId());
	        } catch (Exception $e) {

	        }
        	if (!$uploaded && is_array($image) && isset($image['value'])) {
                    $object->setData('product_view_image_'.$optionID, basename($image['value']));
        	}
        }

    	$format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        if (! $object->getId()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }*/
    
    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
            $select = $read->select()
                ->from(array('main_table' => $this->getMainTable()))
                ->joinright(array('eao' => $this->getTable('eav/attribute_option')), 'eao.option_id = main_table.option_id', array())
                ->join(array('eaov' => $this->getTable('eav/attribute_option_value')), 'eaov.option_id = eao.option_id', array('option_id', 'value'))
                ->where('main_table.id = ?', $value);

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);

        return $this;
    }
    
    public function loadFromOptionId(Mage_Core_Model_Abstract $object, $value)
    {
     $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
            $select = $read->select()
                    ->from(array('main_table' => $this->getMainTable()))
                    ->joinright(array('eao' => $this->getTable('eav/attribute_option')), 'eao.option_id = main_table.option_id', array())
                    ->join(array('eaov' => $this->getTable('eav/attribute_option_value')), 'eaov.option_id = eao.option_id', array('option_id', 'value'))
                    ->where('eao.option_id = ?', $value);

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }
        $this->_afterLoad($object);

        return $this;   
    }
}

?>
