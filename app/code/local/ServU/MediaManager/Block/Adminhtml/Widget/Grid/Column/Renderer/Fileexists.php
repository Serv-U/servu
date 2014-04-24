<?php

class ServU_MediaManager_Block_Adminhtml_Widget_Grid_Column_Renderer_Fileexists extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {
    public function render(Varien_Object $row) {
        $file_id = $row->getData('id');
        if(Mage::helper('mediamanager')->fileExists($file_id)){
            return 'Exists';
        }
        return '<strong style="color: red;">File Is Missing!</strong>';
    }
}
?>
