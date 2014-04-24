<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manage
 *
 * @author dustinmiller
 */
class SD_AdvancedAttributes_Block_Manage_Configurables extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'manage_configurables';
        $this->_blockGroup = 'advancedattributes';
        $this->_headerText = Mage::helper('advancedattributes')->__('Manage Configurables');
        $this->_addButtonLabel = Mage::helper('advancedattributes')->__('Load');
        parent::__construct();
        
    }

    protected function _prepareLayout()
    {
        /**
         * Display store switcher if system has more one store
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->setChild('store_switcher',
                $this->getLayout()->createBlock('adminhtml/store_switcher')
                    ->setUseConfirm(false)
                    ->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
            );
        }
        $this->setChild('grid', $this->getLayout()->createBlock('advancedattributes/manage_configurables_grid', 'configurables.grid'));
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }
}
?>
