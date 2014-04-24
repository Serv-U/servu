<?php

/**
 * All attributes option array
 *
 * @category   Inchoo
 * @package    Productfilter
 * @author     Milovan Gal <milovan.gal@inchoo.net>
 */
class Inchoo_Productfilter_Model_Attributes
{

    public function toOptionArray()
    {
    	$ignoreAttributes = array('sku', 'name', 'attribute_set_id', 'type_id', 'qty', 'price', 'status', 'visibility');
    	
    	$collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter();
        
        $result = array();   
    	foreach ($collection as $model) {
    		if(in_array($model->getAttributeCode(), $ignoreAttributes)) {
    			continue;
    		}
    		$productCollection = Mage::getModel('catalog/product')->getCollection();
    		$productCollection->addAttributeToSelect(array($model->getAttributeCode()));
    		//$productCollection->addAttributeToFilter($model->getAttributeCode(), array('gt' => 0));
    		//if(count($productCollection->getData()) > 0) {
    			//$result[] = array('value' => $model->getAttributeCode(), 'label'=>$model->getFrontendLabel() . ' => (' .$model->getAttributeCode().')' );
                        $result[] = array('value' => $model->getAttributeCode(), 'label'=>$model->getAttributeCode() );
    		//}   
        }
        
        $result = $this->sortmulti($result, 'label', 'asc');

        return $result;

    }
    
    //$order has to be either asc or desc
    private function sortmulti ($array, $index, $order, $natsort=FALSE, $case_sensitive=FALSE) {
            if(is_array($array) && count($array)>0) {
                foreach(array_keys($array) as $key)
                $temp[$key]=$array[$key][$index];
                if(!$natsort) {
                    if ($order=='asc')
                        asort($temp);
                    else   
                        arsort($temp);
                }
                else
                {
                    if ($case_sensitive===true)
                        natsort($temp);
                    else
                        natcasesort($temp);
                if($order!='asc')
                    $temp=array_reverse($temp,TRUE);
                }
                foreach(array_keys($temp) as $key)
                    if (is_numeric($key))
                        $sorted[]=$array[$key];
                    else   
                        $sorted[$key]=$array[$key];
                return $sorted;
            }
        return $sorted;
    }
}
