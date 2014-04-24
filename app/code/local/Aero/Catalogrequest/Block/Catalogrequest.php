<?php
class Aero_Catalogrequest_Block_Catalogrequest extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCatalogrequest()     
     { 
        if (!$this->hasData('catalogrequest')) {
            $this->setData('catalogrequest', Mage::registry('catalogrequest'));
        }
        return $this->getData('catalogrequest');
        
    }
}