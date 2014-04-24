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
class SD_advancedattributes_Helper_Image extends Mage_Catalog_Helper_Image
{
    public function initImage($attributeName, $imageFile=null)
    {
        $this->_reset();
        $this->_setModel(Mage::getModel('sd_manager/ManufacturerInfoImage'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        //$this->setProduct($product);
        $this->setImageFile('catalog/manufacturers/' . $imageFile);

        return $this;

    }
    
    public function initFilterItems(Mage_Catalog_Model_Layer_State $state, $items) {
        $filters = $state->getFilters();

        foreach($items as $item) {
            foreach($filters as $itemInState) {
                if ($item->getFilter() == $itemInState->getFilter()
                                && $item->getValue() == $itemInState->getValue()) {
                    $item->setInState(true);
                    $itemInState->setOutputInCheckbox($this->getNotUseFilter() ? false : true);
                }
            }
        }
    }

    public function __toString()
    {
    	try {
            $this->_getModel()->setBaseFile( $this->getImageFile() );

            if( $this->_getModel()->isCached() ) {
                return $this->_getModel()->getUrl();
            } else {
                if( $this->_scheduleRotate ) {
                    $this->_getModel()->rotate( $this->getAngle() );
                }

                if ($this->_scheduleResize) {
                    $this->_getModel()->resize();
                }

                $url = $this->_getModel()->saveFile()->getUrl();
            }
        } catch( Exception $e ) {
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }
}

?>
