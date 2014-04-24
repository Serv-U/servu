<?php
/**
 * @desc MediaManager Index redirects to Browse Controller
 * @author andrewprendergast
 */
class ServU_MediaManager_IndexController extends Mage_Core_Controller_Front_Action {
    
    public function indexAction () {
        Mage::app()->getResponse()->setRedirect( Mage::getBaseUrl().'mediamanager/browse');
    }
    
}
?>