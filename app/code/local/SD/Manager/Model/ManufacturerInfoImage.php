<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManufacturerInfoImage
 *
 * @author dustinmiller
 */
class SD_Manager_Model_ManufacturerInfoImage extends Mage_Catalog_Model_Product_Image
{
    /**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @return Mage_Catalog_Model_Product_Image
     */

    public function setBaseFile($file)
    {
        if (($file) && (0 !== strpos($file, '/', 0))) {
            $file = '/' . $file;
        }

        $baseDir = Mage::getBaseDir('media');

        if ('/no_selection' == $file) {
            $file = null;
        }

        if ($file) {
            if ((!file_exists($baseDir . $file)) || !$this->_checkMemory($baseDir . $file)) {
                $file = null;
            }
        }
        $baseFile = $baseDir . $file;

        if ((!$file) || (!file_exists($baseFile))) {
            throw new Exception(Mage::helper('catalog')->__('Image file not found'));
        }

        $this->_baseFile = $baseFile;
        // build new filename (most important params)

        $path = array(
            Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath(),
            'cache', Mage::app()->getStore()->getId(), $path[] = $this->getDestinationSubdir()
        );

        if((!empty($this->_width)) || (!empty($this->_height)))
            $path[] = "{$this->_width}x{$this->_height}";
            //append prepared filename
            $this->_newFile = implode('/', $path) . '/' . basename($file);
            
            return $this;
    }
}

?>