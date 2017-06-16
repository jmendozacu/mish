<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Block_Adminhtml_Fields_Color extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $output=parent::_getElementHtml($element);
        if (!Mage::registry('mColorPicker')) {
            $output.='
            <script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/welldone/default/vendor/jquery/jquery-1.7.2.min.js"></script>
            <script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/welldone/default/js/admin/colorpicker/mColorPicker.js"></script>
            <script type="text/javascript">
                jQuery.noConflict();
                 jQuery.fn.mColorPicker.defaults.imageFolder="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/welldone/default/js/admin/colorpicker/images/";
            </script>
            ';
            Mage::register('mColorPicker', 1);
        }
		$output .= '
        <script type="text/javascript">
            jQuery(function(){
                 jQuery("#'.$element->getHtmlId().'").attr("data-hex", true).mColorPicker();
            })
        </script> ';
        return $output;
    }
}