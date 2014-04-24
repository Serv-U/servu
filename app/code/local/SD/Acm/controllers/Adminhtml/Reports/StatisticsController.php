<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Filters
 *
 * @author dustinmiller
 */
class SD_ACM_Adminhtml_Reports_StatisticsController extends Mage_Adminhtml_Controller_Report_Abstract
{
 
    public function _initAction()  {
        $this->loadLayout()
        ->_addBreadcrumb(Mage::helper('sd_acm')->__('ACM'), Mage::helper('sd_acm')->__('ACM'));
        return $this;
    }
 
    public function dailyAction() {
        $this->_title($this->__('Abandoned Carts Mailer'))->_title($this->__('Reports'))->_title($this->__('Daily Statistics'));
 
        $this->_initAction()
        ->_setActiveMenu('newsletter/acm')
        ->_addBreadcrumb(Mage::helper('sd_acm')->__('Abandoned Carts Mailer Report'), Mage::helper('sd_acm')->__('Abandoned Carts Mailer Report'));
 
        $gridBlock = $this->getLayout()->getBlock('adminhtml_reports_daily.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $this->_initReportAction(array(
        $gridBlock,
        $filterFormBlock
        ));
 
        $this->renderLayout();
 
    }
    

    public function exportDailyStatCsvAction() {
        $fileName   = 'dailyStat.csv';
        $grid       = $this->getLayout()->createBlock('acm/adminhtml_reports_daily_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportDailyStatExcelAction() {  
        $fileName   = 'dailyStat.xml';
        $grid       = $this->getLayout()->createBlock('acm/adminhtml_reports_daily_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
 
}

?>
