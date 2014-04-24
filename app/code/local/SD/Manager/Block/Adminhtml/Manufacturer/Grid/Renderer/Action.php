<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Action
 *
 * @author dustinmiller
 */
class SD_Manager_Block_Adminhtml_Manufacturer_Grid_Renderer_Action 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$storeId = null === $row->getData('attribute_value_store_id') ? $row->getData('store_id') : $row->getData('attribute_value_store_id');
		if ($row->getData('id') > 0) {
			$identifier = $row->getData('identifier');
		} else {
			$identifier = SD_Manager_Model_Mysql4_Manufacturer::formatUrlKey($row->getData('value'));
		}
        $urlModel = Mage::getModel('core/url')->setStore($storeId);
        $href = $urlModel->getUrl($row->getData('attribute_code').'/'.$identifier, array('_current'=>false, '___store'=>$storeId));
        return '<a href="'.$href.'" target="_blank">'.$this->__('Preview').'</a>';
    }
}

?>