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
class SD_ReviewReminder_Block_Adminhtml_Individual extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {
        $this->_controller = 'adminhtml_individual';
        $this->_blockGroup = 'reviewreminder';
        $this->_headerText = Mage::helper('reviewreminder')->__('Review Reminder Individual Statistics');
        parent::__construct();
        $this->_removeButton('add');   
    }

    protected function _prepareLayout() {
        $this->setChild('grid', $this->getLayout()->createBlock('reviewreminder/adminhtml_individual_grid', 'individual.grid'));
        return parent::_prepareLayout();
    }

    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }
}
?>
