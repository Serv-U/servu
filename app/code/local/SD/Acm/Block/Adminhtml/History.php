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
class SD_Acm_Block_Adminhtml_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        Mage::log('construct');
        $this->_controller = 'adminhtml_history';
        $this->_blockGroup = 'acm';
        $this->_headerText = Mage::helper('sd_acm')->__('Abandoned Cart Mailer History');
        parent::__construct();
        $this->_removeButton('add');   
    }

    protected function _prepareLayout()
    {
        Mage::log('preparelayout');
        $this->setChild('grid', $this->getLayout()->createBlock('acm/adminhtml_history_grid', 'history.grid'));
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        Mage::log('getgrid');
        return $this->getChildHtml('grid');
    }
}
?>
