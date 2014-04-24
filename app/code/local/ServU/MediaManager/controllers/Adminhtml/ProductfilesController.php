<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductfilesController
 *
 * @author dustinmiller
 */
class ServU_MediaManager_Adminhtml_ProductfilesController extends Mage_Adminhtml_Controller_Action {
        
    public function indexAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function productfilesgridAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
}

?>