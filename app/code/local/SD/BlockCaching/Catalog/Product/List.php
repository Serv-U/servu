<?php
class SD_BlockCaching_Catalog_Product_List extends Mage_Catalog_Block_Product_List 
{  
    protected function _construct() {
        $this->addData(array('cache_lifetime' => 86400,));
    }
}
