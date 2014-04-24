<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManufacturerInfoController
 *
 * @author dustinmiller
 */
class SD_Manager_ManufacturerInfoController 
    extends Mage_Core_Controller_Front_Action
{
    public function indexAction() 
    {
		echo '123';
    }

    public function viewAction()
    {
        $manufacturerInfoId = $this->getRequest()
            ->getParam('id', $this->getRequest()->getParam('id', false));

        $option_id = $this->getRequest()->getParam('option_id', false);

        $helper = Mage::helper('sd_manager/ManufacturerInfo');

        /* @var $helper SD_Manager_Helper_ManufacturerInfo */
        if ($helper->loadAttributePage($this, $manufacturerInfoId, 'manufacturer', $option_id)) {
                $helper->renderAttributePage($this);
        } else {
                $this->_forward('noRoute');
        }
        
        if(Mage::registry('manufacturerupdate')) {
            $this->setFlag('', 'no-renderLayout', true);
            
            $response = array(
                'col_main_content' 				=> $this->getLayout()->getBlock('content')->toHtml(),
                'filter_content' 	=> $this->getLayout()->getBlock('advancedattributes.catalog.leftnav')
                                ->setTemplate('advancedattributes/catalog/layer/view.phtml')->toHtml()
            );

            if ($priceRangeBlock = $this->getLayout()->getBlock('layer_filter_price_range')) {
                if ($priceRangeBlock->canBeShown()) {
                    /** @var $priceRangeBlock SD_AdvancedAttributes_Block_Layer_Filter_Price_Range */
                    $response['pricing_content'] = $priceRangeBlock->getConfig();
                }
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            $this->getResponse()->setHeader('Content-type', 'application/json');
        }
    }

    public function viewAllAction()
    {   
        Mage::register('attribute_code', 'manufacturer');
        $helper = Mage::helper('sd_manager/ManufacturerInfo');
        /* @var $helper SD_Manager_Helper_ManufacturerInfo */
       	$helper->renderAllAttributesPage($this);
    }
    
    public function hasAction($action) {
        if($this->getRequest()->getPost('advancedattributes') == 'true' && !Mage::registry('manufacturerupdate') && $action == 'view') {
            Mage::register('manufacturerupdate',true);
        }
         
        return is_callable(array($this, $this->getActionMethodName($action)));
    }

}

?>