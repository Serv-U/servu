<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * DISCLAIMER
 *
 *
 * @category   Primeinteractive
 * @package    Primeinteractive_Mapp
 * @version    1.0
 * @copyright   Copyright (c) 2012 Prime Interactive, Inc.
 */

class Primeinteractive_Mapp_Block_Adminhtml_Mapp_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'mapp';
        $this->_controller = 'adminhtml_mapp';

        $this->_updateButton('delete', 'label', Mage::helper('mapp')->__('Delete Item'));
        
        $this->_addButton('email_mapp', array(
            'label'     => Mage::helper('adminhtml')->__('Send Client Email'),
            'onclick' => "emailMapp()",
        ));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        //Javascript for Save/Continue button
        $js_text = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
        
        //Javascript for Email Client button
        $mapp_id = $this->getRequest()->getParam('id');
            if($mapp_id != ''){
                //Verify coupon has not expired
                if($expired = Mage::getModel('mapp/mapp')->isCouponExpiredByMappID($mapp_id)) {
                    $js_text .= "
                        function emailMapp(){
                            alert('This coupon code is no longer valid. Please create another request or modify the creation date before sending email to client.');
                        }
                    ";
                } else {
                //Create confirmation alert before sending Email
                    $email_mapp_url = $this->getUrl('*/*/emailmapp',array('id'=>$mapp_id));
                    $js_text .= "
                        function emailMapp(){
                            var yn = window.confirm('Are you sure you want to send an email to this customer?');
                            if(yn == true){
                                setLocation('{$email_mapp_url}');
                            }
                        }
                    ";
                }
            } else {
                //Require entry to be saved to database before trying to send email
                $js_text .= "
                    function emailMapp(){
                        alert('Please save request before sending email');
                    }
                ";
            }
        
        //Set Javascript
        $this->_formScripts[] = $js_text;
    }

    public function getHeaderText()
    {
        if( Mage::registry('mapp_data') && Mage::registry('mapp_data')->getId() ) {
            return Mage::helper('mapp')->__("Edit Mapp Request", $this->htmlEscape(Mage::registry('mapp_data')->getTitle()));
        } else {
            return Mage::helper('mapp')->__('Add Mapp Request');
        }
    }
}