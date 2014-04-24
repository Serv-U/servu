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
class SD_AdvancedAttributes_Model_Mysql4_Options extends Mage_Core_Model_Mysql4_Abstract {
    
    public function _construct() {    
        $this->_init('advancedattributes/options', 'id');  
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        //process images upload
        $value = $object->getData('product_list_image');
       
        $path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'attributes' . DS . 'list' . DS;

        if (is_array($value) && !empty($value['delete'])) {
	            //remove the file
	            unlink(Mage::getBaseDir('media') . DS . $value['value']);
	            $object->setData('product_list_image', '');
        } else {
        	$uploaded = false;
        	try {
                    //try to make the upload
	            $uploader = new Varien_File_Uploader('product_list_image');
	            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	            $uploader->setAllowRenameFiles(false);
	            $uploader->save($path);
	            if (($uploaded = $uploader->getUploadedFileName()) > '') {
	            	$object->setData('product_list_image', $uploaded);
	            }
	        } catch (Exception $e) {

	        }
        	if (!$uploaded && is_array($value) && isset($value['value'])) {
                    $object->setData('product_list_image', basename($value['value']));
        	}
        }
        
        $value = $object->getData('product_view_image');
        
        $path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'attributes' . DS . 'view' . DS;

        if (is_array($value) && !empty($value['delete'])) {
	            //remove the file
	            unlink(Mage::getBaseDir('media') . DS . $value['value']);
	            $object->setData('product_view_image', '');
        } else {
        	$uploaded = false;
        	try {
                    //try to make the upload
	            $uploader = new Varien_File_Uploader('product_view_image');
	            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	            $uploader->setAllowRenameFiles(false);
	            $uploader->save($path);
	            if (($uploaded = $uploader->getUploadedFileName()) > '') {
	            	$object->setData('product_view_image', $uploaded);
	            }
	        } catch (Exception $e) {

	        }
        	if (!$uploaded && is_array($value) && isset($value['value'])) {
                    $object->setData('product_view_image', basename($value['value']));
        	}
        }
        
        $value = $object->getData('layered_image');
        
        $path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'attributes' . DS . 'layered' . DS;

        if (is_array($value) && !empty($value['delete'])) {
	            //remove the file
	            unlink(Mage::getBaseDir('media') . DS . $value['value']);
	            $object->setData('layered_image', '');
        } else {
        	$uploaded = false;
        	try {
                    //try to make the upload
	            $uploader = new Varien_File_Uploader('layered_image');
	            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	            $uploader->setAllowRenameFiles(false);
	            $uploader->save($path);
	            if (($uploaded = $uploader->getUploadedFileName()) > '') {
	            	$object->setData('layered_image', $uploaded);
	            }
	        } catch (Exception $e) {

	        }
        	if (!$uploaded && is_array($value) && isset($value['value'])) {
                    $object->setData('layered_image', basename($value['value']));
        	}
        }

    	$format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        if (! $object->getId()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;

        //process images upload
        /*$value = array();
        $path = array();
        $value[0] = $object->getData('product_list_image');
        $value[1] = $object->getData('product_view_image');
        $value[2] = $object->getData('layered_image');
        
        $path[0] = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'attributes' . DS . 'list' . DS;
        $path[1] = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'attributes' . DS . 'view' . DS;
        $path[2] = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'attributes' . DS . 'layered' . DS;

        for($i = 0; $i < count($value); $i++) {
            if (is_array($value[$i]) && !empty($value[$i]['delete'])) {
                //remove the file
                unlink(Mage::getBaseDir('media') . DS . $value[$i]['value']);
                $object->setData($value[$i], '');
            } else {
                $uploaded = false;
                try {
                    //try to make the upload
                    $uploader = new Varien_File_Uploader($value[$i]);
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->save($path[$i]);
                    if (($uploaded = $uploader->getUploadedFileName()) > '') {
                        $object->setData($value[$i], $uploaded);
                    }
                } catch (Exception $e) {

                }
                if (!$uploaded && is_array($value[$i]) && isset($value[$i]['value'])) {
                    $object->setData($value[$i], basename($value[$i]['value']));
                }
            }   
        }*/
    }
    
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
