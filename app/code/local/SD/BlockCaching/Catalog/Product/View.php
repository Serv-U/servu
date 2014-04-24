<?php
/**
 * Rewriting Product View block
 */
class SD_BlockCaching_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{

    protected function _construct()
    {
        $this->addData(array(
        'cache_lifetime' => 86400,
        'cache_tags'     => array(Mage_Catalog_Model_Product::CACHE_TAG. "_" . $this->getProduct()->getId()),
        'cache_key'      => $this->getProduct()->getId(),
        ));
    }
    
}
