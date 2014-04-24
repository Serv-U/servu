<?php
/**
 *  
 */
class ServU_MediaManager_Block_Browse_Results extends Mage_Core_Block_Template 
{
    public function __construct()
    {
        parent::__construct();
        
        $model = Mage::getModel('mediamanager/browse');
        $collection = $model->filterResults($this->getRequest());
        
        //TEMPORARY WORK AROUND SINCE RETURNING EMPTY COLLECTION CAUSES PAGINATION ERROR...
        if(empty($collection)){
            $collection = $model->getCollection()
                    ->addFieldToFilter('file_status',3);
        }
        
        $this->setCollection($collection);
    }
 
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
 
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager = Mage::helper('mediamanager')->setPaginationIncrements($pager);
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
 
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}