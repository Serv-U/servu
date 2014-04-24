<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Files
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Block_Adminhtml_Files extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_files';
        $this->_blockGroup = 'mediamanager';
        $this->_headerText = Mage::helper('mediamanager')->__('Manage Files');
        parent::__construct();
    }

}
?>
