<?php

class ServU_MediaManager_Block_Adminhtml_Files_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $model = Mage::registry('mediamanager_data');
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('display_form', array('legend' => Mage::helper('mediamanager')->__('Basic Information')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        } else {
            $fieldset->addField('file_id', 'hidden', array('name' => 'file_id'));
        }
        
        $fieldset->addField('file_title', 'text', array(
            'label'     => Mage::helper('mediamanager')->__('Title'),
            'title'     => Mage::helper('mediamanager')->__('Title'),
            'name'      => 'file_title',
            'required'  => true,
            'style'     => 'width: 420px;',
        ));

        $fieldset->addField('file_status', 'select', array(
            'label' => Mage::helper('mediamanager')->__('Status'),
            'name' => 'file_status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('mediamanager')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('mediamanager')->__('Disabled'),
                ),
            ),
        ));

        $fieldset->addField('file_name', 'label', array(
            'label'     => Mage::helper('mediamanager')->__('Current File'),
            'title'     => Mage::helper('mediamanager')->__('Current File'),
            'name'      => 'file_name',
            //'required'  => true,
            'style'     => 'width: 420px;',
        ));
        
        $select_file_fieldset = $form->addFieldset('select_file_fieldset', array('legend' => Mage::helper('mediamanager')->__('Select File')));
        
//        $fieldset->addType('extension','ServU_MediaManager_Lib_Varien_Data_Form_Element_Extension');
//        $fieldset->addField('file_extension', 'extension', array(
//            'label'     => Mage::helper('mediamanager')->__('Change File'),
//            'name'      => 'file_extension',
//            'onchange'  => 'alert("working");',
////            'required'      => false,
////            'value'         => $this->getLastEventLabel($lastEvent),
//        ));

        $file_type = $select_file_fieldset->addField('file_type', 'select', array(
            'label' => Mage::helper('mediamanager')->__('File Type'),
            'name' => 'file_type',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('mediamanager')->__('Upload File'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('mediamanager')->__('Embedded Video'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('mediamanager')->__('External Url'),
                ),
            ),
        ));     
        
        $upload_file = $select_file_fieldset->addField('upload_file', 'file', array(
            'label'     => Mage::helper('mediamanager')->__('Upload File'),
            'title'     => Mage::helper('mediamanager')->__('Upload File'),
            'required'  => false,
            'read_only' => true,
            'name'      => 'upload_file',
        ));
        
        $embed_video = $select_file_fieldset->addField('embed_video', 'text', array(
            'label' => Mage::helper('mediamanager')->__('Video Url'),
            'title' => Mage::helper('mediamanager')->__('Video Url'),
            'name'  => 'embed_video',
            'style' => 'width: 420px;',
        ));
        
        $external_url = $select_file_fieldset->addField('external_url', 'text', array(
            'label' => Mage::helper('mediamanager')->__('External Url'),
            'title' => Mage::helper('mediamanager')->__('External Url'),
            'name'  => 'external_url',
            'style' => 'width: 420px;',
        ));
        
        //Set dependency for file type
        $this->setChild('form_after',$this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($file_type->getHtmlId(),$file_type->getName())
            ->addFieldMap($upload_file->getHtmlId(),$upload_file->getName())
            ->addFieldMap($embed_video->getHtmlId(),$embed_video->getName())
            ->addFieldMap($external_url->getHtmlId(),$external_url->getName())
            ->addFieldDependence($upload_file->getName(),$file_type->getName(),0)
            ->addFieldDependence($external_url->getName(),$file_type->getName(),2)
            ->addFieldDependence($embed_video->getName(),$file_type->getName(),1) );
        
        $details_fieldset = $form->addFieldset('detailed_fieldset', array('legend' => Mage::helper('mediamanager')->__('Details')));
        
        $details_fieldset->addField('file_manufacturer_date', 'date', array(
            'name'      => 'file_manufacturer_date',
            'title'     => Mage::helper('mediamanager')->__('Manufacturer Date'),
            'label'     => Mage::helper('mediamanager')->__('Manufacturer Date'),
            'format' => 'YYYY-MM-DD',
            'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),            
        ));        
        
        $details_fieldset->addField('no_follow', 'select', array(
            'label' => Mage::helper('mediamanager')->__('No Follow'),
            'name' => 'no_follow',
            'value'  => '1',
            'values' => array(
                array(
                    'value' => '1',
                    'select' => 'select',
                    'label' => Mage::helper('mediamanager')->__('Enabled'),
                ),
                array(
                    'value' => '0',
                    'label' => Mage::helper('mediamanager')->__('Disabled'),
                ),
            ),
        ));
        
        $details_fieldset->addField('file_description', 'textarea', array(
            'label' => Mage::helper('mediamanager')->__('Internal Description/Notes'),
            'title' => Mage::helper('mediamanager')->__('Internal Description/Notes'),
            'name'  => 'file_description',
            'style' => 'width: 420px;',
        ));
        
        if ( Mage::registry('mediamanager_data') ) {
            $form->setValues(Mage::registry('mediamanager_data')->getData());
        }
        
        return parent::_prepareForm();
    }

}
