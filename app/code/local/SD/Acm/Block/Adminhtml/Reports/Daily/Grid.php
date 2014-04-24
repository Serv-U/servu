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
class SD_Acm_Block_Adminhtml_Reports_Daily_Grid extends SD_Acm_Block_Adminhtml_Reports_Grid_Abstract {
    
    protected $_columnGroupBy = 'period';

    public function __construct() {
        parent::__construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName() {
        return 'sd_acm/reports_daily_collection';
    }

    protected function _prepareColumns() {
	if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);
		
        $this->addColumn('period', array(
            'header'    => Mage::helper('sd_acm')->__('Period'),
            'align'     => 'left',
            'index'     => 'period',
            'type'      => 'text'
        ));
        
        $this->addColumn('sum_abandoned_carts', array(
            'header'    => Mage::helper('sd_acm')->__('# Abandoned Carts'),
            'index'     => 'sum_abandoned_carts',
            'type'      => 'number'
        ));
        
        $this->addColumn('carts_initial_amount', array(
            'header'        => Mage::helper('sd_acm')->__('$ Initial Cart'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'carts_initial_amount',
            'rate'          => $rate
        ));
        
        $this->addColumn('avg_initial_amount', array(
            'header'        => Mage::helper('sd_acm')->__('$ Avg Initial Cart'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'avg_initial_amount',
            'rate'          => $rate
        ));
        //
        
        $this->addColumn('sum_products', array(
            'header'    => Mage::helper('sd_acm')->__('# Products'),
            'index'     => 'sum_products',
            'type'      => 'number'
        ));
        
        $this->addColumn('sum_recovered', array(
            'header'    => Mage::helper('sd_acm')->__('# Recovered'),
            'index'     => 'sum_recovered',
            'type'      => 'number'
        ));
        
        $this->addColumn('percent_recovered', array(
            'header'    => Mage::helper('sd_acm')->__('% Recovered'),
            'index'     => 'percent_recovered',
            'type'      => 'number'
        ));
        
        $this->addColumn('sum_ordered', array(
            'header'    => Mage::helper('sd_acm')->__('# Ordered'),
            'index'     => 'sum_ordered',
            'type'      => 'number'
        ));
        
        $this->addColumn('percent_ordered', array(
            'header'    => Mage::helper('sd_acm')->__('% Ordered'),
            'index'     => 'percent_ordered',
            'type'      => 'number'
        ));
        
        $this->addColumn('amount_ordered', array(
            'header'    => Mage::helper('sd_acm')->__('$ Ordered'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'amount_ordered',
            'rate'          => $rate
        ));

        $this->addColumn('avg_amount_ordered', array(
            'header'    => Mage::helper('sd_acm')->__('$ Avg Ordered'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'avg_amount_ordered',
            'rate'          => $rate
        ));       
        
        $this->addColumn('percent_amount_ordered', array(
            'header'    => Mage::helper('sd_acm')->__('% $ Ordered'),
            'index'     => 'percent_amount_ordered',
            'type'      => 'number'
        ));
        
        $this->addColumn('sum_products_ordered', array(
            'header'    => Mage::helper('sd_acm')->__('# Products Ordered'),
            'index'     => 'sum_products_ordered',
            'type'      => 'number'
        ));
        
        $this->addColumn('percent_products_ordered', array(
            'header'    => Mage::helper('sd_acm')->__('% Product Ordered'),
            'index'     => 'percent_products_ordered',
            'type'      => 'number'
        ));
        
        $this->addExportType('*/*/exportDailyStatCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportDailyStatExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
