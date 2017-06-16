<?php
class VES_BannerManager_Model_Wysiwyg_Config extends Varien_Object
{
    /**
     * Wysiwyg behaviour
     */
    const WYSIWYG_ENABLED = 'enabled';
    const WYSIWYG_HIDDEN = 'hidden';
    const WYSIWYG_DISABLED = 'disabled';

    /**
     * Return Wysiwyg config as Varien_Object
     *
     * Config options description:
     *
     * enabled:                 Enabled Visual Editor or not
     * hidden:                  Show Visual Editor on page load or not
     * use_container:           Wrap Editor contents into div or not
     * no_display:              Hide Editor container or not (related to use_container)
     * translator:              Helper to translate phrases in lib
     * files_browser_*:         Files Browser (media, images) settings
     * encode_directives:       Encode template directives with JS or not
     *
     * @param $data Varien_Object constructor params to override default config values
     * @return Varien_Object
     */
    public function getConfig($data = array())
    {
        $config = new Varien_Object();

        $config->setData(array(
            'enabled'                       => $this->isEnabled(),
            'hidden'                        => $this->isHidden(),
            'use_container'                 => false,
            'add_variables'                 => false,
            'add_widgets'                   => false,
            'no_display'                    => false,
            'translator'                    => Mage::helper('cms'),
            'files_browser_window_url'      => Mage::getSingleton('adminhtml/url')->getUrl('cmspro/adminhtml_wysiwyg_images/index'),
            'files_browser_window_width'    => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
            'files_browser_window_height'   => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height'),
            'encode_directives'             => true,
            'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('cmspro/adminhtml_wysiwyg/directive'),
            'popup_css'                     => Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/dialog.css',
            'content_css'                   => Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/content.css',
            'width'                         => '100%',
            'plugins'                       => array()
        ));

        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));

        if (is_array($data)) {
            $config->addData($data);
        }

        Mage::dispatchEvent('cms_wysiwyg_config_prepare', array('config' => $config));

        return $config;
    }

    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        return in_array(Mage::getStoreConfig('cms/wysiwyg/enabled'), array(self::WYSIWYG_ENABLED, self::WYSIWYG_HIDDEN));
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return Mage::getStoreConfig('cms/wysiwyg/enabled') == self::WYSIWYG_HIDDEN;
    }
}
