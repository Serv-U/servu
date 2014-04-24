<?php
require_once Mage::getModuleDir('controllers', 'Mage_Catalog').'/CategoryController.php';

/**
 * Category controller used to return to the browser html of layered navigation and
 * html of the content. Also return price range config if enabled.
 */
class SD_AdvancedAttributes_CategoryController extends Mage_Catalog_CategoryController {
        
    public function viewAction() {
        $this->setFlag('', 'no-renderLayout', true);
        parent::viewAction();

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

    /**
     * Use this controller only to override the view action behaviour
     * and only when this is post request with particular flag in it.
     *
     * @param $action
     * @return bool
     */
    public function hasAction($action) {
        if ($action != 'view') {
            return false;
        } else {
            return $this->getRequest()->isPost()
                            && $this->getRequest()->getPost('advancedattributes') == 'true';
        }
    }
}
?>
