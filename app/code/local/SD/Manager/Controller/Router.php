<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Router
 *
 * @author dustinmiller
 */
class SD_Manager_Controller_Router 
    extends Mage_Core_Controller_Varien_Router_Abstract
{
    /***
         * Adds extra router to check for url keys from the manager_manufacturers table
	 * The request must be of the form /manufacturer/[url_key]/
	 * For example /manufacturer/sony/
	 *
	 * @param $observer
	 */

    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();

        $sdManager = new SD_Manager_Controller_Router();
        $front->addRouter('info', $sdManager);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $params = trim($request->getPathInfo(), '/');
        $params = explode('/', $params);

        $manufacturerRoute = $params[0];
        $urlKey = null;

        if(isset($params[1])) {
        	$urlKey = $params[1];
        }

        $attributes = Mage::getModel('sd_manager/manufacturer');
	/* @var $attributes SD_Manager_Model_Manufacturer */

        if ($manufacturerRoute == 'manufacturer') {
                
	        if ($manufacturerInfoId = $attributes->checkIdentifierInPages($manufacturerRoute, $urlKey, Mage::app()->getStore()->getId())) { 
		        $request->setModuleName('manufacturerinfo')
		            ->setControllerName('ManufacturerInfo')
		            ->setActionName('view')
		            ->setParam('id', $manufacturerInfoId);
                        $request->setAlias(
                            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                            $manufacturerRoute .'/'.$urlKey
			);
                        return true;
	        }
        	//second, search in attributes for a possible match
	        if (($option_id = $attributes->getOptionIdFromIdentifier($manufacturerRoute, $urlKey, Mage::app()->getStore()->getId())) > 0) {
                    //if ($attributes->getData('attribute_code') > '') {
                    //we have another winnnner!!!
                    $request->setModuleName('manufacturerinfo')
                            ->setControllerName('ManufacturerInfo')
			    ->setActionName('view')
			    ->setParam('attribute_code', $manufacturerRoute)
			    ->setParam('option_id', $option_id);
                    $request->setAlias(
                        Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
			$manufacturerRoute.'/'.$urlKey
                    );
                    return true;
	        }
	        //well.. just display all the values
        	$request->setModuleName('manufacturerinfo')
	            ->setControllerName('ManufacturerInfo')
	            ->setActionName('viewAll')
	            ->setParam('attribute_code', $manufacturerRoute);
		$request->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                    $manufacturerRoute.'/'.$urlKey
		);
		return true;
        }
        //we didn't find anything acceptable in this router, resume search in others
        return false;
    }
}
?>