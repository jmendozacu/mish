<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Block_Adminhtml_Fields_Fonts extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $output = parent::_getElementHtml($element);

        ob_start();
        ?>
            <span id="<?php echo $element->getHtmlId()?>_view" style="font-size:30px;line-height: 30px; display:block;padding:8px 0 0 0">Lorem ipsum $99.99</span>

            <script type="text/javascript">
                jQuery.noConflict();
                jQuery(function(){
                    jQuery("#<?php echo $element->getHtmlId()?>").change(function(){
                        jQuery("#<?php echo $element->getHtmlId()?>_view").css({ fontFamily: jQuery("#<?php echo $element->getHtmlId()?>").val().replace("+"," ") });
                        jQuery("<link />",{href:"http://fonts.googleapis.com/css?family="+jQuery("#<?php echo $element->getHtmlId()?>").val(),rel:"stylesheet",type:"text/css"}).appendTo("head");
                    }).keyup(function(){
                        jQuery("#<?php echo $element->getHtmlId()?>_view").css({ fontFamily: jQuery("#<?php echo $element->getHtmlId()?>").val().replace("+"," ") });
                        jQuery("<link />",{href:"http://fonts.googleapis.com/css?family="+jQuery("#<?php echo $element->getHtmlId()?>").val(),rel:"stylesheet",type:"text/css"}).appendTo("head");
                    }).keydown(function(){
                        jQuery("#<?php echo $element->getHtmlId()?>_view").css({ fontFamily: jQuery("#<?php echo $element->getHtmlId()?>").val().replace("+"," ") });
                        jQuery("<link />",{href:"http://fonts.googleapis.com/css?family="+jQuery("#<?php echo $element->getHtmlId()?>").val(),rel:"stylesheet",type:"text/css"}).appendTo("head");
                    });
                    jQuery("#<?php echo $element->getHtmlId()?>").trigger("change");
                })
            </script>
        <?php
        $output.=ob_get_contents();
        ob_clean();
        return $output;
    }
}