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

class SD_Acm_Block_Adminhtml_Reports_Daily extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'acm';
        $this->_controller = 'adminhtml_reports_daily';
        $this->_headerText = Mage::helper('sd_acm')->__('Daily Statistics');
        $this->setTemplate('report/grid/container.phtml');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('sd_acm')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/daily', array('_current' => true));
    }
}
?>
