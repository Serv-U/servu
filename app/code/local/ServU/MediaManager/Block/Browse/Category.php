<?php
/**
 *  
 */
class ServU_MediaManager_Block_Browse_Category extends Mage_Core_Block_Template 
{
    public function __construct()
    {
        parent::__construct();
        $collection = Mage::getModel('catalog/category')
                        ->getCollection()
                        ->addAttributeToSelect('*')
                        //Order categories alphabetically
                        //->setOrder('name', 'asc')
                        ->addIsActiveFilter();
        
        //TEMPORARY WORK AROUND. GRID SHOULD NOT DISPLAY ON DEFAULT, BUT RETURNING NULL CAUSES PAGINATION ERROR...
        if(empty($collection)){
            $collection = Mage::getModel('mediamanager/browse')->getCollection()->addFieldToFilter('file_status',3);
        }
        
        $this->setCollection($collection);
    }
 
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
 
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(90=>90,'all'=>'all'));
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