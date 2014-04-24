<?php
/**
 * @desc MediaManager Browse Controller
 * @author andrewprendergast
 */
class ServU_MediaManager_BrowseController extends Mage_Core_Controller_Front_Action {
    
    public function indexAction () {
        $this->loadLayout();
        $this->getLayout()->getBlock('mediamanager_menu')->setPagetitle('Browsing All Media Files');
        $this->renderLayout();
    }
    
    /**
     * @desc MediaType Index Page
     */
    public function mediatypeAction () {
        $this->loadLayout();
        $this->getLayout()->getBlock('mediamanager_menu')->setPagetitle('Select Media Type');
        $this->renderLayout();
    }
    
    /**
     * @desc MediaType Results Page
     */
    public function medAction () {
        if($type = $this->getRequest()->getParam('type')) {
            $this->loadLayout();

            $pagetitle = 'Browsing by ' . ucfirst($type);
            $this->getLayout()->getBlock('mediamanager_menu')->setPagetitle($pagetitle);

            $this->renderLayout();
        } else {
            //Redirect if type not set
            Mage::app()->getResponse()->setRedirect( Mage::getBaseUrl().'mediamanager/browse/mediatype');
        }
    }

    /**
     * @desc Manufacturer Index Page
     */
    public function manufacturerAction () {
        $this->loadLayout();
        $this->getLayout()->getBlock('mediamanager_menu')->setPagetitle('Select Manufacturer');
        $this->renderLayout();
    }    

    /**
     * @desc Manufacturer Results Page
     */
    public function manAction () {
        if($id = $this->getRequest()->getParam('id')) {
            $this->loadLayout();
            
            //Set page title
            $pagetitle = 'Browsing by ' . Mage::getModel('mediamanager/browse')->getManufacturerName($id);
            $this->getLayout()->getBlock('mediamanager_menu')->setPagetitle($pagetitle);
            
            //Generate link to return to 'browse by manufacturer' page
            $changeLink = '<a href="'.Mage::getBaseUrl().'mediamanager/browse/manufacturer">Select a Different Manufacturer</a>';
            $this->getLayout()->getBlock('mediamanager_menu')->setChangeLink($changeLink);
            
//            //Set variables for restricting search to select manufacturer
//            $this->getLayout()->getBlock('mediamanager_menu')->setRestrictSearchType('man');
//            $this->getLayout()->getBlock('mediamanager_menu')->setRestrictSearch(true);
//            $this->getLayout()->getBlock('mediamanager_menu')->setRestrictSearchID($id);
            
            $this->renderLayout();
        } else {
            //Redirect if id not set
            Mage::app()->getResponse()->setRedirect( Mage::getBaseUrl().'mediamanager/browse/manufacturer');
        }
    }
    
    /**
     * @desc Category Index Page
     */
    public function categoryAction () {
        $this->loadLayout();
        $this->getLayout()->getBlock('mediamanager_menu')->setPagetitle('Select Category');
        $this->renderLayout();
    }
    
    /**
     * @desc Category Results Page
     */
    public function catAction () {
        if($id = $this->getRequest()->getParam('id')) {
            $this->loadLayout();
 
            $_category = Mage::getModel('catalog/category')->load($id);
            $pagetitle = 'Browsing by ' . $_category->getName();
            $changeLink = '<a href="'.Mage::getBaseUrl().'mediamanager/browse/category">Select a Different Category</a>';
            
            $this->getLayout()->getBlock('mediamanager_menu')->setChangeLink($changeLink);
            $this->getLayout()->getBlock('mediamanager_menu')->setPagetitle($pagetitle);
            
            $this->renderLayout();
        } else {
            //Redirect if id not set
            Mage::app()->getResponse()->setRedirect( Mage::getBaseUrl().'mediamanager/browse/category');
        }
    }

    /**
     * @desc Search Results Page
     */
    public function searchAction () {
        if($searchText = $this->getRequest()->getParam('mediaManagerSearchText')) {
            $this->loadLayout();
            $this->getLayout()->getBlock('mediamanager_menu')->setPagetitle('Search Results for ' . $searchText);
            $this->getLayout()->getBlock('mediamanager_menu')->setSearchText($searchText);
            $this->renderLayout();
        } else {
            //Redirect if search is not set
            Mage::app()->getResponse()->setRedirect( Mage::getBaseUrl().'mediamanager/browse');
        }
    }
}
?>