<?php

class WP_CustomMenu_Block_System_Config_Form_Field_Position extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);
        $javaScript = "
            <script type=\"text/javascript\">
                function wpToggleMenuPosition()
                {
                    var valMenuPosition = $('{$element->getHtmlId()}').value;
                    if (!valMenuPosition || valMenuPosition < 1)
                    {
                        $('row_custom_menu_general_top_static_block').hide();
                        $('row_custom_menu_popup_bottom_offset').hide();
                        $('row_custom_menu_popup_top_offset').show();
                        $('row_custom_menu_popup_right_offset_min').show();
                        $('row_custom_menu_general_show_home_link').show();
                    }
                    else
                    {
                        $('row_custom_menu_popup_top_offset').hide();
                        $('row_custom_menu_popup_right_offset_min').hide();
                        $('row_custom_menu_general_show_home_link').hide();
                        $('row_custom_menu_general_top_static_block').show();
                        $('row_custom_menu_popup_bottom_offset').show();
                    }
                }
                Event.observe(window, 'load', function() {
                    wpToggleMenuPosition();
                });
                Event.observe('{$element->getHtmlId()}', 'change', function(){
                    wpToggleMenuPosition();
                });
            </script>";
        $html .= $javaScript;
        return $html;
    }
}
