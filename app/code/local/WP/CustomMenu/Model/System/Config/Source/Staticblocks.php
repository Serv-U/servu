<?php

class WP_CustomMenu_Model_System_Config_Source_Staticblocks
{
    private $_options;

    public function toOptionArray()
    {
        if (!$this->_options)
        {
            $this->_options = Mage::getResourceModel('cms/block_collection')
                ->load()
                ->toOptionArray();

            array_unshift($this->_options,
                array(
                    'value'=>'',
                    'label' => Mage::helper('custommenu')->__('--- Use standard Top Menu ---')
                )
            );
        }
        return $this->_options;
    }
}
