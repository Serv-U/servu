<?php
class ServU_MediaManager_Block_Adminhtml_Files_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'mediamanager';
//        $this->_mode = 'edit';
        $this->_controller = 'adminhtml_files';
        
        $this->_updateButton('delete', 'label', Mage::helper('mediamanager')->__('Delete File'));

//Removed and added save button in order to manually display "Please Wait" gif and prevent duplicate submissions
//        $this->_updateButton('save', 'label', Mage::helper('mediamanager')->__('Save File'));
        $this->_removeButton('save');
        $this->_addButton('save', array(
            'label'     => Mage::helper('adminhtml')->__('Save File'),
            'onclick'   => 'saveFile()',
            'class'     => 'save',
        ), -100);
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
            
            document.observe('dom:loaded', function(){
                //Clear out null dates (uploading from spreadsheet causes date formatting errors in edit form)
                manufacturer_date = document.getElementById('file_manufacturer_date').value;
                //alert(manufacturer_date);
                if(manufacturer_date == '11/30/-1'){
                    document.getElementById('file_manufacturer_date').value = '';
                }

//                setExtensionDependentField();
            });
            
//            function setExtensionDependentField(){
//document.getElementById('file_type').selectedIndex = 1;
//                alert('test');
//            }

            function saveFile(){
                if(validateFormFields() == true){
                    manuallyDisplayLoader();
                    editForm.submit();
                }
            }
            
            function saveAndContinueEdit(){
                if(validateFormFields() == true){
                    manuallyDisplayLoader();
                    editForm.submit($('edit_form').action+'back/edit/');
                }
            }
            
            //Display loading graphic
            function manuallyDisplayLoader() {
                var loader = document.getElementById('loading-mask');
                loader.style.display = 'block';
                loader.style.width = window.innerWidth + 'px';
                loader.style.height = window.innerHeight + 'px';
                loader.style.top = 0;
            }
            
            //Check for title and file size
            function validateFormFields(){
                if($('file_title').value == ''){
                    alert('Please enter a file name.');
                    return false;
                }
                
                if($('upload_file').value != '' && $('upload_file').files[0].size >= 19728948){
                    alert('This file is too large to upload.');
                    return false;
                }
                return true;
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('mediamanager_data') && Mage::registry('mediamanager_data')->getFileTitle() ) {
            return Mage::helper('mediamanager')->__("Edit File '%s'", $this->htmlEscape(Mage::registry('mediamanager_data')->getFileTitle()));
        }
        else {
            return Mage::helper('mediamanager')->__('Edit File Properties');
        }
    }

}
