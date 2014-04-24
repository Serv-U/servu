<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    
 * @package     _storage
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ShoppingFeeds feed import model
 *
 * @category    ShoppingFeeds
 * @package     ShoppingFeeds_Feed
 */
class ShoppingFeeds_Feed_Model_Import extends Mage_Core_Model_Abstract
{
    const SEPARATOR = "\t";
    const LINE_END  = "\r\n";
    const ENCLOSURE = '"';
    const COLLECTION_PAGE_SIZE = 500;    
    
    /**
     * Attribute sources
     *
     * @var array
     */
    protected $_attributeSources = array();
    
    /**
     * Cron action
     */
    public function dispatch()
    {
        $this->processImport();
    }

    /**
     * @desc Process feed import
     */
    public function processImport()
    {
        try {
            $file = $this->_createFile();
            if ($file) {
                //Extra files exist on Bing FTP
                //$this->_deleteFtpFiles();
                $this->_sendFile($file);
    //To delete local file after sending via ftp
    //            if (!$this->_deleteFile($file)) {
    //                Mage::throwException(Mage::helper('shoppingfeeds_feed/data')->__("FTP: Can't delete files"));
    //            }
            }
        } catch (Exception $e) {
            Mage::log('Caught exception: ' . $e->getMessage(), null);
        }
    }
    
    /**
     * Check attribute source
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $value
     * @return bool
     */
    protected function _checkAttributeSource($product, $value)
    {
        if (!array_key_exists($value, $this->_attributeSources)) {
            $this->_attributeSources[$value] = $product->getResource()->getAttribute($value)->usesSource();
        }
        return $this->_attributeSources[$value];
    }
    

    /**
     * Create temp csv file and write export
     *
     * @return mixed
     */
    protected function _createFile()
    {
        $dir = $this->_getTmpDir();
        $fileName = Mage::getStoreConfig($this->getFeedSettings('feed_filename'));
        if (!$dir || !$fileName) {
            Mage::log('Filename/Folder error: See _createFile() in Import.php', null, 'shoppingfeed_error.txt');
            return false;
        }

        if (!($attributes = $this->_getImportAttributes()) || count($attributes) <= 0) {
            Mage::log('Attributes error: See _createFile() in Import.php', null, 'shoppingfeed_error.txt');
            return false;
        }

        $headers = array_keys($attributes);

        $file = new Varien_Io_File;
        $file->checkAndCreateFolder($dir);
        $file->cd($dir);
        $file->streamOpen($fileName, 'w+');
        $file->streamLock();
        //Double quotes enclosure (Optional for TheFind but Discouraged by Bing)...        
        //$file->streamWriteCsv($headers, self::SEPARATOR, self::ENCLOSURE);
        $file->streamWriteCsv($headers, self::SEPARATOR, chr(0));

        //Find number of pages
        $productCollectionPages = $this->_getProductCollection();
        $pageNumbers = $productCollectionPages->getLastPageNumber();
        unset($productCollectionPages);

        //Loop through collection for product information
        for ($i = 1; $i <= $pageNumbers; $i++) {
            $productCollection = $this->_getProductCollection();
            $productCollection->addAttributeToSelect($attributes);
            $productCollection->setCurPage($i)->load();
            
            foreach ($productCollection as $product) {
                $attributesRow = array();
                foreach ($attributes as $key => $value) {
                    if ($this->_checkAttributeSource($product, $value)) {
                        if (is_array($product->getAttributeText($value))) {
                            $attributesRow[$key] = implode(', ', $product->getAttributeText($value));
                        } else {
                            $attributesRow[$key] = $product->getAttributeText($value);
                        }
                    } else {
                        $attributesRow[$key] = $product->getData($value);
                    }
                    
                    $attributesRow[$key] = Mage::helper('shoppingfeeds_feed/data')->formatFeedFields($attributesRow[$key], $value);
                }
                //Double quotes enclosure (Optional for TheFind but Discouraged by Bing)...
                //$file->streamWriteCsv($attributesRow, self::SEPARATOR, self::ENCLOSURE);
                $file->streamWriteCsv($attributesRow, self::SEPARATOR, chr(0));
            }
            unset($productCollection);
        }

        $file->streamUnlock();
        $file->streamClose();

        if ($file->fileExists($fileName)) {
            return $fileName;
        }
        return false;
    }

    /**
     * @desc Get search visible and enabled products only
     * @return object 
     */
    protected function _getProductCollection(){
        return Mage::getResourceModel('catalog/product_collection')
                    ->setPageSize(self::COLLECTION_PAGE_SIZE)
//                    ->addAttributeToFilter('in_shoppingfeeds', 1)
                    ->addAttributeToFilter('visibility', array('eq' => 4))
                    ->addAttributeToFilter('status', 1);
    }
    
    /**
     * Send file to remote ftp server
     *
     * @param string $fileName
     */
    protected function _sendFile($fileName)
    {
        $dir         = $this->_getTmpDir();
        $ftpServer   = Mage::getStoreConfig($this->getFeedSettings('ftp_server'));
        $ftpUserName = Mage::getStoreConfig($this->getFeedSettings('ftp_user'));
        $ftpPass     = Mage::getStoreConfig($this->getFeedSettings('ftp_password'));
        $ftpPath     = trim(Mage::getStoreConfig($this->getFeedSettings('ftp_path')), '/');
        if ($ftpPath) {
            $ftpPath = $ftpPath.'/';
        }
        
        $ch = curl_init();

        //Set username and password separate from url so that special characters can be used in passwords
	curl_setopt($ch, CURLOPT_USERPWD, "$ftpUserName:$ftpPass"); 
        curl_setopt($ch, CURLOPT_URL, $ftpServer.'/'.$ftpPath.$fileName);
        //curl_setopt($ch, CURLOPT_URL, 'ftp://'.$ftpUserName.':'.$ftpPass.'@'.$ftpServer.'/'.$ftpPath.$fileName);
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_INFILE, fopen($dir.$fileName, 'r'));
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($dir.$fileName));
        curl_exec($ch);
        
        if(curl_errno($ch)){
            Mage::log('Unable to send file to FTP: See _sendFile() in Import.php', null, 'shoppingfeed_error.txt');
            Mage::log('Error Details:' . curl_error($ch), null);
        }
        
        curl_close($ch);
    }

    /**
     * Delete all files in current feed ftp directory
     *
     * @return bool
     */
    protected function _deleteFtpFiles()
    {
        if (is_callable('ftp_connect')) {
            $ftpServer   = Mage::getStoreConfig($this->getFeedSettings('ftp_server'));
            $ftpUserName = Mage::getStoreConfig($this->getFeedSettings('ftp_user'));
            $ftpPass     = Mage::getStoreConfig($this->getFeedSettings('ftp_password'));
            $ftpPath     = trim(Mage::getStoreConfig($this->getFeedSettings('ftp_path')), '/');
            if ($ftpPath) {
                $ftpPath = $ftpPath.'/';
            }

            try {
                $connId = ftp_connect($ftpServer);

                $loginResult = ftp_login($connId, $ftpUserName, $ftpPass);
                if (!$loginResult) {
                    Mage::log('Unable to login to FTP: See _deleteFtpFiles() in Import.php', null, 'shoppingfeed_error.txt');
                    return false;
                }
                ftp_pasv($connId, true);

                $ftpDir = $ftpPath?$ftpPath:'.';
                $nlist = ftp_nlist($connId, $ftpDir);
                if ($nlist === false) {
                    Mage::log('Unable to get list of files: See _deleteFtpFiles() in Import.php', null, 'shoppingfeed_error.txt');
                    return false;
                }
                foreach ($nlist as $file) {
                    //Delete non xml files
                    if (!preg_match('/\.[xX][mM][lL]$/', $file) && $file != '.' && $file != '..') {
                    //Delete txt files only
                    //if (preg_match('/\.[tT][xX][tT]$/', $file)) {
                        ftp_delete($connId, $file);
                        //Mage::log('File deleted: ' . $file, null, 'shoppingfeed_error.txt');
                    }
                }

                ftp_close($connId);
            } catch (Exception $e) {
                Mage::log($e->getMessage());
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Current tmp directory
     *
     * @return string
     */
    protected function _getTmpDir()
    {
        return Mage::getBaseDir('var') . DS . 'export' . DS . 'shoppingfeeds_feed' . DS;
    }

    /**
     * Delete tmp file
     *
     * @param string $fileName
     * @return true
     */
    protected function _deleteFile($fileName)
    {
        $dir  = $this->_getTmpDir();
        $file = new Varien_Io_File;
        if ($file->fileExists($dir . $fileName, true)) {
            $file->cd($dir);
            $file->rm($fileName);
        }
        return true;
    }
}
