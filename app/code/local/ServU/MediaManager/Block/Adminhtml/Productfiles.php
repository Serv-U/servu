<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductFiles
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Block_Adminhtml_Productfiles extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    
    public function __construct()
    {
        $this->_controller = 'adminhtml_productfiles';
        $this->_blockGroup = 'mediamanager';
        $this->_headerText = Mage::helper('mediamanager')->__('Serv-U Media Manager');
        parent::__construct();
        $this->removeButton('add');
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
        $this->setChild('grid', $this->getLayout()->createBlock('mediamanager/adminhtml_productfiles_grid', 'productfiles.grid'));
        return parent::_prepareLayout();
    }
    
    /*
    public function getHeaderHtml(){
        return null;
    }
    */
}
?>
