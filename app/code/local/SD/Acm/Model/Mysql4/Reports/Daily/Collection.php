<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*SELECT * FROM sd_carts_mailed 
right join sales_flat_quote on sd_carts_mailed.quote_id = sales_flat_quote.entity_id 
where sales_flat_quote.is_active = 1 and sales_flat_quote.customer_id <> '' or sd_carts_mailed.status <> '3'*/

class SD_Acm_Model_Mysql4_Reports_Daily_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    const REPORT_PERIOD_TYPE_DAY    = 'DAY';
    const REPORT_PERIOD_TYPE_MONTH  = 'MONTH';
    const REPORT_PERIOD_TYPE_YEAR   = 'YEAR'; 
    protected $_period ='DAY';
    protected $_to='1900-01-01';
    protected $_from='1900-01-01';
    protected $_storeIds= array();
    protected $_aggregateColumns= array();
    protected $_isTotals = false;
    protected $_sortOrderField ='period';
    protected $_sortOrderDir ='asc';
    
    public function setPeriod($period){
        if (isset($period)) {
                $this->_period= $period;
        }
        return $this;
    }
    
    public function setDateRange($from, $to){
            //$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            ///Mage::log('debug from:'.$from.'  to:'.$to ,null,'sql.log'); 
            //$this->_from = Mage::app()->getLocale()->date($from, '%Y-%m-%d');
            //$this->_to = Mage::app()->getLocale()->date($to, '%Y-%m-%d');
            //Mage::log('debug from:'.$this->_from.'  to:'.$this->_to  ,null,'sql.log'); 
            $this->_from = $from;
            $this->_to = $to;
            return $this;
    }

    public function addStoreFilter($storeFilter){
            $this->_storeIds= $storeFilter;
            return $this;
    }
    
    public function isTotals($isTotals){
            $this->_isTotals= $isTotals;
            return $this;
    }
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('sd_acm/acm');
    }
    
    public function addSortOrder($sortOrderField, $sortOrderDir){

        if (isset($sortOrderField)) {
            $this->_sortOrderField = $sortOrderField;
            $this->_sortOrderDir = $sortOrderDir;
        }
        return $this;
    }
    
    public function getData($select=null)
    {
        if ($this->_data === null) {
            $this->_renderFilters()->_renderOrders()->_renderLimit();
            
            if(!is_null($select)){
                    $this->_select = $select;
            }
            
            $interval = 'DATE';
            switch ($this->_period) {
                case self::REPORT_PERIOD_TYPE_DAY :
                    $interval = '%M %d %Y';
                    break;
                case self::REPORT_PERIOD_TYPE_MONTH:
                    $interval = '%M %Y';
                    break;
                case self::REPORT_PERIOD_TYPE_YEAR:
                    $interval = '%Y';       
                    break;
            }

            $this->_select ="SELECT DATE_FORMAT(scm.created_at, '".$interval."') period, 
                IFNULL(SUM(scm.initial_cart_amount), 0), 
                SUM(scm.initial_cart_amount) AS carts_initial_amount,  
                (SUM(scm.initial_cart_amount) / COUNT(scm.id)) AS avg_initial_amount,
                IFNULL(SUM(sfo.base_subtotal), 0) AS amount_ordered, 
                IFNULL((SUM(sfo.base_subtotal) / COUNT(sfo.entity_id)), 0) AS avg_amount_ordered,
                IFNULL((SUM(sfo.base_subtotal)/ SUM(scm.initial_cart_amount) * 100), 0) as percent_amount_ordered, 
                COUNT(scm.id) AS sum_abandoned_carts, 
                COUNT(sfo.entity_id) AS sum_ordered, 
                IFNULL(((COUNT(sfo.entity_id)) / (COUNT(scm.id)) * 100),0) AS percent_ordered, 
                SUM(sfq.items_qty) AS sum_products, 
                IFNULL(SUM(sfo.total_qty_ordered), 0) AS sum_products_ordered, 
                IFNULL((SUM(sfo.total_qty_ordered)/sum(sfq.items_qty) * 100), 0) AS percent_products_ordered, 
                SUM(scm.has_recovered) AS sum_recovered,
                IFNULL((SUM(scm.has_recovered)/count(scm.id) * 100), 0) AS percent_recovered
                FROM sd_carts_mailed AS scm
                LEFT join sales_flat_quote AS sfq ON sfq.entity_id = scm.quote_id 
                LEFT join sales_flat_order AS sfo ON sfo.quote_id = scm.quote_id 
                WHERE scm.created_at >= DATE('".$this->_from."') AND scm.created_at <= DATE('".$this->_to."') ";
                if (is_array($this->_storeIds)){
                    $this->_select = $this->_select."AND  scm.store_id in (".implode(",",$this->_storeIds).") "; 
                }
                $this->_select = $this->_select."GROUP BY DATE_FORMAT(scm.created_at, '".$interval."') ".
                "ORDER BY ".$this->_sortOrderField." ".$this->_sortOrderDir." ";

            //Mage::log($this->_select ,null,'sql.log'); 
            $this->_data = $this->_fetchAll($this->_select);
            $this->_afterLoadData();
        }
        
        return $this->_data;
    }
    
    public function load($select=null, $printQuery = false, $logQuery = false){
        if ($this->isLoaded()) {
            return $this;
        }
        
        $this->_beforeLoad();
        $this->_renderFilters()
             ->_renderOrders()
             ->_renderLimit();

        $this->printLogQuery($printQuery, $logQuery);
        $data = $this->getData($select);
        $this->resetData();

        if (is_array($data)) {
            foreach ($data as $row) {
                $item = $this->getNewEmptyItem();
                if ($this->getIdFieldName()) {
                    $item->setIdFieldName($this->getIdFieldName());
                }
                $item->addData($row);
                $this->addItem($item);
            }
        }

        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }
}

?>
