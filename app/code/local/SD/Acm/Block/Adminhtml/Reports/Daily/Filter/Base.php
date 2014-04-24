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
class SD_Acm_Block_Adminhtml_Reports_Daily_Filter_Base extends Mage_Adminhtml_Block_Widget_Form {
    
    protected $_reportTypeOptions = array();
    protected $_fieldVisibility = array();
    protected $_fieldOptions = array();

    public function setFieldVisibility($fieldId, $visibility) {
        $this->_fieldVisibility[$fieldId] = (bool)$visibility;
    }

    public function getFieldVisibility($fieldId, $defaultVisibility = true) {
        if (!array_key_exists($fieldId, $this->_fieldVisibility)) {
            return $defaultVisibility;
        }
        return $this->_fieldVisibility[$fieldId];
    }

    public function setFieldOption($fieldId, $option, $value = null) {
        if (is_array($option)) {
            $options = $option;
        } else {
            $options = array($option => $value);
        }
        if (!array_key_exists($fieldId, $this->_fieldOptions)) {
            $this->_fieldOptions[$fieldId] = array();
        }
        foreach ($options as $k => $v) {
            $this->_fieldOptions[$fieldId][$k] = $v;
        }
    }

    public function addReportTypeOption($key, $value) {
        $this->_reportTypeOptions[$key] = $this->__($value);
        return $this;
    }

    protected function _prepareForm() {
        $actionUrl = $this->getUrl('*/*/sales');
        $form = new Varien_Data_Form(
            array('id' => 'filter_form', 'action' => $actionUrl, 'method' => 'get')
        );
        
        $htmlIdPrefix = 'sd_acm_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('sd_acm')->__('Filter')));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
	//Mage::log('debug date'.$dateFormatIso ,null,'sql.log'); 
        $fieldset->addField('store_ids', 'hidden', array(
            'name'  => 'store_ids'
        ));

        $fieldset->addField('period_type', 'select', array(
            'name' => 'period_type',
            'options' => array(
                'DAY'   => Mage::helper('sd_acm')->__('Day'),
                'MONTH' => Mage::helper('sd_acm')->__('Month'),
                'YEAR'  => Mage::helper('sd_acm')->__('Year')
            ),
            'label' => Mage::helper('sd_acm')->__('Show by'),
            'title' => Mage::helper('sd_acm')->__('Show by')
        ));

        $fieldset->addField('from', 'date', array(
            'name'      => 'from',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('sd_acm')->__('From'),
            'title'     => Mage::helper('sd_acm')->__('From'),
            'required'  => true
        ));

        $fieldset->addField('to', 'date', array(
            'name'      => 'to',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('sd_acm')->__('To'),
            'title'     => Mage::helper('sd_acm')->__('To'),
            //'class'		=> 'validation_date_renge',
            'required'  => true
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
	
        $this->setFieldVisibility("{$htmlIdPrefix}show_actual_columns", false);
		
        return parent::_prepareForm();
    }

    protected function _initFormValues() {
        $data = $this->getFilterData()->getData();
        foreach ($data as $key => $value) {
            if (is_array($value) && isset($value[0])) {
                $data[$key] = explode(',', $value[0]);
            }
        }
        $this->getForm()->addValues($data);
        return parent::_initFormValues();
    }

    protected function _beforeToHtml() {
        $result = parent::_beforeToHtml();

        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            foreach ($fieldset->getElements() as $field) {
                if (!$this->getFieldVisibility($field->getId())) {
                    $fieldset->removeField($field->getId());
                }
            }
            foreach ($this->_fieldOptions as $fieldId => $fieldOptions) {
                $field = $fieldset->getElements()->searchById($fieldId);
                if ($field) {
                    foreach ($fieldOptions as $k => $v) {
                        $field->setDataUsingMethod($k, $v);
                    }
                }
            }
        }

        return $result;
    }
	
    protected function _prepareLayout() {
	parent::_prepareLayout();
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        $fieldSetForm = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset');
        $fieldSetForm ->setTemplate('sd_acm/form/fieldset.phtml');
        Varien_Data_Form::setFieldsetRenderer($fieldSetForm);
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
        );

        return $this;
    }
}
