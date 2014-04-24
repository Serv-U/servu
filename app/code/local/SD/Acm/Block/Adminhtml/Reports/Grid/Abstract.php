<?php
/**
* Customer Anlytics
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com and you will be sent a copy immediately.
*
* @category   Anrena
* @package    Anrena_CustomerAnlytics
* @author     Anrena support@anrena.pl
* @copyright  Copyright (c) 2013 Anrena (http://www.anrena.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/ 

class SD_Acm_Block_Adminhtml_Reports_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
{
    const REPORT_PERIOD_TYPE_DAY    = 'DAY';
    const REPORT_PERIOD_TYPE_MONTH  = 'MONTH';
    const REPORT_PERIOD_TYPE_YEAR   = 'YEAR'; 
    
    protected $_resourceCollectionName  = '';
    protected $_currentCurrencyCode     = null;
    protected $_storeIds                = array();
    protected $_aggregatedColumns       = null;

    public function __construct() {
        parent::__construct();
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setUseAjax(false	);
	$this->_resourceCollectionName = 'sd_carts_reports/reports';
        
        if (isset($this->_columnGroupBy)) {
            $this->isColumnGrouped($this->_columnGroupBy, true);
        }
        
        $this->setEmptyCellLabel(Mage::helper('adminhtml')->__('No records found for this period.'));
    }

    public function getResourceCollectionName() {
        return $this->_resourceCollectionName;
    }

    public function getCollection() {
        if (is_null($this->_collection)) {
            $this->setCollection(Mage::getModel('adminhtml/report_item'));
	    }
        return $this->_collection;
    }

    public function addColumn($columnId, $column) {
        return parent::addColumn($columnId, $column);
    }

    protected function _getStoreIds() {
        $filterData = $this->getFilterData();
        if ($filterData) {
            $storeIds = explode(',', $filterData->getData('store_ids'));
        } else {
            $storeIds = array();
        }
        $allowedStoreIds = array_keys(Mage::app()->getStores());
        $storeIds = array_intersect($allowedStoreIds, $storeIds);
        if (empty($storeIds)) {
            $storeIds = $allowedStoreIds;
        }
        $storeIds = array_values($storeIds);

        return $storeIds;
    }

    protected function _prepareCollection() {
        $filterData = $this->getFilterData();
	$this->setCountTotals(false);
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        if ($filterData->getData('from') == null || $filterData->getData('to') == null) {
            $this->setCountSubTotals(false);
            return $this; 
        }
		
	$storeIds = $this->_getStoreIds();

        $orderStatuses = $filterData->getData('order_statuses');
        
        if (is_array($orderStatuses)) {
            if (count($orderStatuses) == 1 && strpos($orderStatuses[0],',')!== false) {
                $filterData->setData('order_statuses', explode(',',$orderStatuses[0]));
            }
        }

        $this->removeColumn("avg_sales");
        $this->removeColumn("avg_order");	

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        //Mage::log('debug from:'.strtotime($filterData->getData('to')).'  to:'.strtotime($filterData->getData('from'))  ,null,'sql.log'); 	
        $dateDiff = ( strtotime($filterData->getData('to')) - strtotime($filterData->getData('from')) )/60/60/24;
        //Mage::log('debug :dateDiff'.$dateDiff  ,null,'sql.log'); 
        $div = 1;
            
        switch ($filterData->getData('period_type')) {
            case self::REPORT_PERIOD_TYPE_DAY :
                $div = 1;
                break;
            case self::REPORT_PERIOD_TYPE_MONTH:
                $div = 30;
                break;
            case self::REPORT_PERIOD_TYPE_YEAR:
                $div = 365;       
                break;
        }

        $isInRange = (float)$dateDiff/(float)$div;
        /*if ($isInRange >=1.0) {
            $this->addColumnAfter('avg_sales', array(
                'header'        => Mage::helper('sd_acm')->__('Average Sale per')." ".Mage::helper('sd_acm')->__($filterData->getData('period_type')),
                'type'          => 'currency',
                'currency_code' => $currencyCode,
                'index'         => 'avg_sales',
                'rate'          => $rate
            ), 'customer_group');
            
            $this->addColumnsOrder('avg_sales', 'customer_group');
            $this->sortColumnsByOrder();

            $this->addColumn('avg_order', array(
                'header'    => Mage::helper('sd_acm')->__('Avg. Number Orders per')." ".Mage::helper('sd_acm')->__($filterData->getData('period_type')),
                'index'     => 'avg_order',
                'type'      => 'number',
                'align'     => 'rigth',
                'sortable'  => false
            ), 'avg_sales');
            
            $this->addColumnsOrder('avg_order', 'avg_sales');
            $this->sortColumnsByOrder();
        } else {
            $this->addColumnAfter('avg_sales', array(
                'header'        => Mage::helper('sd_acm')->__('Average Sale per')." ".Mage::helper('sd_acm')->__($filterData->getData('period_type')),
                'type'          => 'text',
                'index'         => 'avg_sales'
            ), 'customer_group');
            $this->addColumnsOrder('avg_sales', 'customer_group');
            $this->sortColumnsByOrder();
            $this->addColumn('avg_order', array(
                'header'    => Mage::helper('sd_acm')->__('Avg. Number Orders per')." ".Mage::helper('anrena_reports')->__($filterData->getData('period_type')),
                'index'     => 'avg_order',
                'type'      => 'text',
                'sortable'  => false
            ), 'avg_sales');
            $this->addColumnsOrder('avg_order', 'avg_sales');
            $this->sortColumnsByOrder();
        }*/
        
        $resourceCollection = Mage::getResourceModel($this->getResourceCollectionName())
            ->setPeriod($filterData->getData('period_type'))
            ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
            ->addStoreFilter($storeIds)
            ->addSortOrder($this->getRequest()->getParam('sort'), $this->getRequest()->getParam('dir'));

        if ($this->_isExport) {
            $this->setCollection($resourceCollection);
            return $this;
        }

        if ($this->getCountSubTotals()) {
            $this->getSubTotals();
        }
        
        $this->setCollection($resourceCollection);
        $this->setDefaultSort($this->getRequest()->getParam('sort'));
        $this->setDefaultDir($this->getRequest()->getParam('dir'));

        $columnId = $this->getRequest()->getParam('sort');
        $dir      = $this->getRequest()->getParam('dir');

        if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
               $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
        }
        
        return $this;
    }

    public function setStoreIds($storeIds) {
        $this->_storeIds = $storeIds;
        return $this;
    }

    public function getCurrentCurrencyCode() {
        if (is_null($this->_currentCurrencyCode)) {
            $this->_currentCurrencyCode = (count($this->_storeIds) > 0)
                ? Mage::app()->getStore(array_shift($this->_storeIds))->getBaseCurrencyCode()
                : Mage::app()->getStore()->getBaseCurrencyCode();
        }
        return $this->_currentCurrencyCode;
    }

    public function getRate($toCurrency) {
        return Mage::app()->getStore()->getBaseCurrency()->getRate($toCurrency);
    }

    protected function _addOrderStatusFilter($collection, $filterData) {
        $collection->addOrderStatusFilter($filterData->getData('order_statuses'));
        return $this;
    }

    protected function _addCustomFilter($collection, $filterData) {
        return $this;
    }
}
