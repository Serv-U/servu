<?php

require_once Mage::getModuleDir('controllers', 'Mage_CatalogSearch').'/ResultController.php';

/**
 * Result controller used to return to the browser html of layered navigation and
 * html of the content. Also return price range config if enabled.
 */
class SD_AdvancedAttributes_ResultController extends Mage_CatalogSearch_ResultController {

    public function indexAction() {
        $this->setFlag('', 'no-renderLayout', true);
        Mage::register('_singleton/catalogsearch/layer', Mage::getSingleton('advancedattributes/catalog_layer'));
        parent::indexAction();

        $response = array(
            'col_main_content' 				=> $this->getLayout()->getBlock('content')->toHtml(),
            'filter_content' 	=> $this->getLayout()->getBlock('advancedattributes.catalogsearch.leftnav')
                    ->setTemplate('advancedattributes/catalog/layer/view.phtml')->toHtml()
        );

        if ($priceRangeBlock = $this->getLayout()->getBlock('layer_filter_price_range')) {
            if ($priceRangeBlock->canBeShown()) {
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
        if ($action != 'index') {
                return false;
        } else {
                return $this->getRequest()->isPost()
                        && $this->getRequest()->getPost('advancedattributes') == 'true';
        }
    }
}
?>
