<?php
class SD_AdvancedAttributes_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    /*public function getImage() {
        return $this->getFilter()->getImage();
    }*/
    public function getGroupName() {
        return $this->getFilter()->getGroupName();
    }
}