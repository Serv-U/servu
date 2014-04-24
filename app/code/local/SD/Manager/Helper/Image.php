<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Image
 *
 * @author dustinmiller
 */
class SD_Manager_Helper_Image extends Mage_Catalog_Helper_Image
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