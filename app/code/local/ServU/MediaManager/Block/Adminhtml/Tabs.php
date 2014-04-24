<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tabs
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    private $parent;
 
    protected function _prepareLayout()
    {
        //get all existing tabs
        $this->parent = parent::_prepareLayout();
        
        if (Mage::registry('current_product')->getAttributeSetId()) { 
            //add new tab
            $this->addTab('attached_files', array(
                        'label'     => Mage::helper('catalog')->__('Attached Files'),
                        'url'       => $this->getUrl('adminhtml/productfiles', array('_current' => true)),
                        'class'     => 'ajax',
            ));
        }
        
        return $this->parent;
    }
}
?>