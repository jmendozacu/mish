<?php
class Ves_VendorsRma_Model_Cms_Wysiwyg_Config extends Mage_Cms_Model_Wysiwyg_Config
{
    public function getConfig($data = array())
    {
        $config = new Varien_Object();
        
        
        $config->setData(array(
            'enabled'                       => false,
            'hidden'                        => false,
            'use_container'                 => false,
            'add_variables'                 => false,
            'add_widgets'                   => false,
            'no_display'                    => false,
            'forced_root_block'=>"",
            'force_br_newlines' =>  true,
            'force_p_newlines' => false,
            "strict_loading_mode"=>true,
            'mode' => "none",
            'theme' => "advanced",
            'translator'                    => Mage::helper('cms'),
            'encode_directives'             => true,
            'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('widget/cms_wysiwyg/directive'),
            'popup_css'                     =>
                Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/dialog.css',
            'content_css'                   =>
                Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/content.css,'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/ves_vendors/rma/tinymce/blockquote.css',
            'width'                         => '100%',
            'plugins'                       => array(),
            'theme_advanced_buttons2' => "",
            'theme_advanced_buttons3' => "",
            'theme_advanced_buttons1'  => "bold,italic,underline,bullist,numlist,separator,outdent,indent,separator,undo,redo,separator",
            'theme_advanced_toolbar_location' => "top",
            'theme_advanced_toolbar_align' => "left",
            'theme_advanced_path_location' => "bottom",
            'theme_advanced_resize_horizontal' => true,
             'theme_advanced_resizing' => true,
             'apply_source_formatting' => true,
        ));
        


        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));

        if (is_array($data)) {
            $config->addData($data);
        }

        Mage::dispatchEvent('cms_wysiwyg_config_prepare', array('config' => $config));

        return $config;
    }

    public function getConfigSytem(){
        $wysiwygConfig = $this->getConfig();
        $plugins = $wysiwygConfig->getData("plugins");
        $plugins = $wysiwygConfig->setData("plugins",$plugins);
        return $plugins;
    }
}