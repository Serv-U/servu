<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of View
 *
 * @author dustinmiller
 */
class SD_Manager_Block_Layer_View 
    extends Mage_Catalog_Block_Layer_View
{
    protected function _getFilterableAttributes()
    {
        $attributes = $this->getData('_filterable_attributes');

        if (is_null($attributes)) {
            $attributes = $this->getLayer()->getFilterableAttributes();
            
           	foreach ($attributes as $a) {
		        try {
                            if ($a->getAttributeCode() == Mage::registry('attribute_code')) {
            			$attributes->removeItemByKey($a->getId());
                            }
                        } catch (Exception $e) {
            	}
            }

            $this->setData('_filterable_attributes', $attributes);
        }
        return $attributes;
    }
    /**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
    	//var_export(Mage::getSingleton('catalog/layer')->getAttributeInfoPage());
        return Mage::getSingleton('catalog/layer');
    }

}

?>