<?php

class ServU_MediaManager_Block_Adminhtml_Renderer_Size extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
    public function render(Varien_Object $row) {
         return Mage::helper('mediamanager')->formatFileSize($row->getData($this->getColumn()->getIndex()));
    }
}

?>