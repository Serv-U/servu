<?php
/**
 *
 * @author alin_M
 *
 */

class Alin_ProductGift_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup {
	
  public function getDefaultEntities() {
	     
    	return array(
	            'catalog_product' => array(
	                'entity_model'      => 'catalog/product',
	                'attribute_model'   => 'catalog/resource_eav_attribute',
	                'table'             => 'catalog/product',
					'additional_attribute_table' => 'catalog/eav_attribute',
	                'entity_attribute_collection' => 'catalog/product_attribute_collection',                
	                'attributes'        => array(
	                    'sku_of_product_gift' => array(
	                        'group'             => 'Gift For Product Bought',
	                        'label'             => 'SKU of product offered free',
	                        'type'              => 'text',
	                        'input'             => 'text',
	                        'backend'           => '',
	                        'frontend'          => '',
	                        'default'           => '',
	                    	'source'            => '',
	                        'class'             => '',
	                    	'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	                        'visible'           => false,
	                        'required'          => false,
	                        'user_defined'      => false,
	                        'searchable'        => false,
	                        'filterable'        => false,
	                        'comparable'        => false,
	                        'visible_on_front'  => false,
	                        'visible_in_advanced_search' => false,
	                        'unique'            => false
	                    ),
	                	 'is_product_gift_enabled' => array (
	                	 	'group'             => 'Gift For Product Bought',
	                	 	'label'             => 'Is enabled',
	                	 	'type'              => 'int',
	                	 	'input'             => 'boolean',
	                	 	'backend'           => '',
	                	 	'frontend'          => '',
	                	 	'default'           => '',
	                	 	'source'            => '',
	                	 	'class'             => '',
	                	 	'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	                	 	'visible'           => false,
	                	 	'required'          => false,
	                	 	'user_defined'      => false,
	                	 	'searchable'        => false,
	                	 	'filterable'        => false,
	                	 	'comparable'        => false,
	                	 	'visible_on_front'  => false,
	                	 	'visible_in_advanced_search' => false,
	                	 	'unique'            => false
	                	 		 
	                	 ),
	               ),
	           ),
	      );      	
      	
	}    
}
