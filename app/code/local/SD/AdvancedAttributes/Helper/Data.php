<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Data
 *
 * @author dustinmiller
 */
class SD_AdvancedAttributes_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function magentoVersion() {     
        return version_compare(Mage::getVersion(), '1.4', '<');
    }  

    /**
     * Update layered navigation state items.
     * Some applied filter items can't be show as checkbox
     * so they will be passed to the browser as hidden inputs.
     *
     * @param Mage_Catalog_Model_Layer_State $state
     * @param $items
     */
    public function initFilterItems(Mage_Catalog_Model_Layer_State $state, $items) {

        $filters = $state->getFilters();
        /** @var $item Mage_Catalog_Model_Layer_Filter_Item */
        foreach($items as $item) {

            /** @var $itemInState Mage_Catalog_Model_Layer_Filter_Item */
            foreach($filters as $itemInState) {

                //Mage::log($item->getFilter().' '.$itemInState->getFilter());
                //if ($item->getFilter() == $itemInState->getFilter()
                            //&& $item->getValue() == $itemInState->getValue()) {
                    $item->setInState(true);
                    $itemInState->setOutputInCheckbox($this->getNotUseFilter() ? false : true);
                //}
            }
        }
    }

    public function setNotUseFilter($flag = true) {
        $this->notUseFilter = $flag;
        return $this;
    }

    public function getNotUseFilter() {
        return $this->notUseFilter;
    }

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $request;
    protected $notUseFilter = false;
    
}

?>
