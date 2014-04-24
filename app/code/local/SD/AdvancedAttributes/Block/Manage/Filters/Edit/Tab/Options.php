<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manage
 *
 * @author dustinmiller
 */

class SD_AdvancedAttributes_Block_Manage_Filters_Edit_Tab_Options 
    extends Mage_Adminhtml_Block_Widget_Grid
 {
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('optionsGrid');
        $this->setDefaultSort('frontend_label');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        Mage::register('advancedattributes_id', $this->getRequest()->getParam('attribute_id'));
    }
	
       /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'advancedattributes/options_collection';
    }

    protected function _prepareCollection()
    {
        //$collection = Mage::getResourceModel($this->_getCollectionClass());
        //$collection = Mage::getResourceModel('eav/attribute_option');
        $collection = Mage::getModel('eav/entity_attribute_option')->getCollection();
        $collection->getSelect()
                ->join(array('eaov' => 'eav_attribute_option_value'), 'eaov.option_id = main_table.option_id', array('option_id', 'value'))
                ->where('main_table.attribute_id = ?', Mage::registry('advancedattributes_id'))
                ->order('eaov.value');     
        $this->setCollection($collection);
 
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('option_id', array(
            'header'            => Mage::helper('advancedattributes')->__('ID'),
            'width'             => '60px',
            'type'              => 'number',
            'index'             => 'option_id',
            'filter_index'      => 'main_table.option_id',
            ));

        $this->addColumn('value', array(
            'header' => Mage::helper('advancedattributes')->__('Label'),
            'index' => 'value',
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('advancedattributes')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getOptionId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('advancedattributes')->__('Edit'),
                        'url'       => array('base'=> '*/manage_options/edit'),
                        'field'     => 'option_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }
    
    protected function _getSelectedOptions()
    {
        return null;
    }

    public function getRowUrl($row)
    {
        if ($page_id = $row->getData('id')) {
            if ($this->getRequest()->getParam('attribute_code')) {
                return $this->getUrl('*/manage_options/edit', array('id' => $page_id, 'option_id' => $row->getData('option_id'), 'attribute_id' => Mage::registry('advancedattributes_id'), 'attribute_code' => $this->getRequest()->getParam('attribute_code')));
            }
            else {
                return $this->getUrl('*/manage_options/edit', array('id' => $page_id, 'option_id' => $row->getData('option_id'), 'attribute_id' => Mage::registry('advancedattributes_id'), 'attribute_filter_id' => $this->getRequest()->getParam('id')));
            }   
        }
        else {
            if ($this->getRequest()->getParam('attribute_code')) {
                return $this->getUrl('*/manage_options/edit', array('option_id' => $row->getData('option_id'), 'attribute_id' => Mage::registry('advancedattributes_id'), 'attribute_code' => $this->getRequest()->getParam('attribute_code')));
            }
            else {
                return $this->getUrl('*/manage_options/edit', array('option_id' => $row->getData('option_id'), 'attribute_id' => Mage::registry('advancedattributes_id'), 'attribute_filter_id' => $this->getRequest()->getParam('id')));
            }
        }
    }

    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/optionsgrid', array('_current'=>true));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('advancedattributes')->__('Options');
    }

    public function getTabTitle()
    {
        return Mage::helper('advancedattributes')->__('Options');
    }

    public function canShowTab()
    {
        if ($this->getOrder()->getIsVirtual()) {
            return false;
        }
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
