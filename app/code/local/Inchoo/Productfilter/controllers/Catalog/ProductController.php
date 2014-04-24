<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CartController
 *
 * @author dustinmiller
 */
include_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class Inchoo_Productfilter_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{   
    const CONFIG_ATTRIBUTE_CODES = 'attribute_filter_section/grid_export_group/export_attribute';
    public function massExportAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
            $this->_redirect('*/*/index');
        }
        else {
            $baseDir = Mage::getBaseDir();
            $varDir = $baseDir.DS.'var';

            $exportDir = $varDir.DS.'export/product_grid_export.csv';
            
            $attributes = explode(',',Mage::getStoreConfig(self::CONFIG_ATTRIBUTE_CODES));
            
            $numAttributes = count($attributes);
            $j = 0;
            $mage_csv = new Varien_File_Csv(); //mage CSV
            $products_row = array();
            $header = array();
            $header['sku'] = "sku";
            //$header['name'] = "name";
            //$header['url'] = "url";
            //$header['price'] = "price";
            
            for($i = 0; $i < $numAttributes; $i++) {
                $header[$attributes[$i]] = $attributes[$i];
                $attributes[$i] = str_replace('_',' ',$attributes[$i]);
                $attributes[$i] = ucWords($attributes[$i]);
                $attributes[$i] = 'get'.str_replace(' ','',$attributes[$i]);
            }	

            $products_row[] = $header;
            try {
                
                foreach ($productIds as $productId) {
                    $product = Mage::getSingleton('catalog/product')->load($productId);
                    
                    $data = array();
                    $data['sku'] = $product->getSku();
                    //$data['name'] = $product->getName();
                    //$data['url'] = $product->getProductUrl();
                    //$data['price'] = $product->getPrice();
                    
                    foreach($attributes as $attribute) {
                        //$data[$attribute] = $product->$attribute();
                        //$attrValue = $attribute->getAttributeCode();
                        //$value = $attrValue->getValue($product);
                        $value = Mage::getModel('catalog/product')->load($product->getId())->$attribute();
                        $data[$attribute] = $value;
                        
                    }
                    $products_row[] = $data;
                }
                $mage_csv->saveData($exportDir, $products_row);
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            $this->_redirect('*/*/index');

            /*
            //write headers to the csv file
            $attributes = explode(',',Mage::getStoreConfig(self::CONFIG_ATTRIBUTE_CODES));
            $content = '"id","name","url","sku"';
            $numAttributes = count($attributes);
            $j = 0;
            for($i = 0; $i < $numAttributes; $i++) {
                if(++$j <> $numAttributes + 1) {
                    $content .= ",";
                } 
                $content .= '"'.$attributes[$i].'"';
                $attributes[$i] = str_replace('_',' ',$attributes[$i]);
                $attributes[$i] = ucWords($attributes[$i]);
                $attributes[$i] = 'get'.str_replace(' ','',$attributes[$i]);
            }
            
            $content .= "\r\n";
            
            try {
                foreach ($productIds as $productId) {
                    $x = 0;
                    $product = Mage::getSingleton('catalog/product')->load($productId);

                    $content .= '"'.$product->getId().'","'.$product->getName().'","'.$product->getProductUrl().'","'.$product->getSku().'"';
                    /*foreach($attributes as $attribute) {
                        if(++$x <> $numAttributes + 1) {
                            $content .= ',';
                        }
                        $content .= '"'.$product->$attribute().'"';
                         
                    }
                    $content .= "\r\n";
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('index');
            }
            $this->_prepareDownloadResponse('export.csv', $content, 'text/csv');*/
        }

    }
}?>