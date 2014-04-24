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

class ServU_ClearanceManagement_Block_Adminhtml_Migration extends Mage_Adminhtml_Block_Widget_Grid_Container {
    
    public function __construct()
    {   
        $this->_controller = 'adminhtml_migration';
        $this->_blockGroup = 'clearancemanagement';
        $this->_headerText = Mage::helper('clearancemanagement')->__('Migrate Products');
        parent::__construct();
        $this->_removeButton('add');
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
        $this->setChild('grid', $this->getLayout()->createBlock('clearancemanagement/adminhtml_migration_grid', 'migration.grid'));
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
