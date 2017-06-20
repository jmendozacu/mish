<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.1
 * @revision  181
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


abstract class Mirasvit_Fpc_Model_Container_Abstract
{
    const CONTAINER_ID_PREFIX = 'FPC_CONATAINER';
    const HTML_NAME_PATTERN   = '/<!--\{(.*?)\}-->/i';
    const EMPTY_VALUE         = 'empty';

    protected $_definition       = null;
    protected static $_layoutXml = null;
    protected $_hash             = null;

    public function __construct($definition, $block)
    {
        $this->_definition = $definition;

        $this->_definition['block_name'] = $block->getNameInLayout();
        $this->_definition['cache_id']   = $block->getCacheKey();
        $this->_definition['layout']     = $this->_generateBlockLayoutXML($block->getNameInLayout());

        return $this;
    }

    public function getBlockReplacerHtml($html)
    {
        return $this->_getStartReplacerTag().$html.$this->_getEndReplacerTag();
    }

    protected function _getStartReplacerTag()
    {
        return '<!--{'.$this->getDefinitionHash().'}-->';
    }

    protected function _getEndReplacerTag()
    {
        return '<!--/{'.$this->getDefinitionHash().'}-->';
    }

    public function getDefinitionHash()
    {
        if ($this->_hash == null) {
            $this->_hash = $this->_definition['block'].'_'.md5($this->_definition['block_name'].'_'.$this->_definition['cache_id']);
        }

        return $this->_hash;
    }

    protected function _generateBlockLayoutXML($blockName)
    {
        if (self::$_layoutXml == null) {
            self::$_layoutXml = Mage::app()->getLayout()->getUpdate()->asSimplexml();
        }
        $sections   = self::$_layoutXml->xpath("//block[@name='{$blockName}'] | //reference[@name='{$blockName}']");

        $layoutXml  = '';
        foreach($sections as $section) {
            $layoutXml .= $this->_generateSubBlockLayoutXml($section);
        }

        $layout = new Mage_Core_Model_Layout();
        $layout->getUpdate()->addUpdate($layoutXml);
        $layout->generateXml();
        $layoutXml = $layout->getXmlString();

        return $layoutXml;
    }

    protected function _generateSubBlockLayoutXml($section)
    {
        $layoutXml = $section->asXML();
        foreach ($section->xpath("block") as $block) {
            foreach (self::$_layoutXml->xpath("//reference[@name='{$block->getBlockName()}']") as $subSection) {
                $layoutXml .= self::_generateSubBlockLayoutXml($subSection);
            }
        }

        return $layoutXml;
    }

    public function applyToContent(&$content)
    {
        $pattern = '/'.preg_quote($this->_getStartReplacerTag(), '/').'(.*?)'.preg_quote($this->_getEndReplacerTag(), '/').'/ims';
        $html    = $this->getBlockHtml();
        $content = preg_replace($pattern, str_replace('$', '\\$', $html), $content, 1);

        return $this;
    }

    public function getBlockHtml()
    {
        $html = $this->loadCache();
        if ($html) {
            Mage::log('Load block from cache: '.$this->_definition['block'].' '.$this->_definition['container'], null, Mirasvit_Fpc_Model_Processor::DEBUG_LOG);
        } else {
            $html = $this->_renderBlock();

            $this->saveCache($html);
            Mage::log('Rendering block: '.$this->_definition['block'].' '.$this->_definition['container'], null, Mirasvit_Fpc_Model_Processor::DEBUG_LOG);
        }

        if ($html == self::EMPTY_VALUE) {
            $html = '';
        }

        return $html;
    }

    protected function _renderBlock()
    {
        Mage::getSingleton('core/layout');

        $layout = new Mage_Core_Model_Layout($this->_definition['layout']);

        Mage::unregister('_singleton/core/layout');
        Mage::register('_singleton/core/layout', $layout, true);

        $layout->setBlock('head', new Varien_Object());
        $layout->generateBlocks();

        $block = $layout->getBlock($this->_definition['block_name']);
        $html  = $block->toHtml();

        return $html;
    }

    protected function _getCacheId()
    {
        if ($this->_getIdentifier()) {
            return self::CONTAINER_ID_PREFIX.'_'.$this->getDefinitionHash().md5($this->_getIdentifier());
        }

        return false;
    }

    protected function _getIdentifier()
    {
        return false;
    }

    public function getCacheId()
    {
        return $this->_getCacheId();
    }

    public function saveCache($blockContent)
    {
        $cacheId = $this->_getCacheId();
        if ($cacheId !== false) {
            $this->_saveCache($blockContent, $cacheId);
        }
        return $this;
    }

    public function loadCache()
    {
        $id = $this->_getCacheId();
        return Mirasvit_Fpc_Model_Cache::getCacheInstance()->load($id);
    }

    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        $tags[] = Mirasvit_Fpc_Model_Processor::CACHE_TAG;
        if (is_null($lifetime)) {
            $lifetime = $this->_definition['cache_lifetime'] ?
                $this->_definition['cache_lifetime'] : false;
        }

        if (!$lifetime) {
            $lifetime = Mage::getSingleton('fpc/config')->getLifetime();
        }

        if ($data == '') {
            $data = ' ';
        }

        Mirasvit_Fpc_Model_Cache::getCacheInstance()->save($data, $id, $tags, $lifetime);

        return $this;
    }

    public function getDependenceHash($dependences)
    {
        $hash = array();

        if (!is_array($dependences)) {
            $dependences = explode(',', $dependences);
        }

        foreach ($dependences as $dependence) {
            $hash[] = $dependence;
            switch ($dependence) {
                case 'customer':
                    $customer   = Mage::getSingleton('customer/session');
                    $hash[] = $customer->getCustomerId();
                    break;

                case 'customer_group':
                    $customer   = Mage::getSingleton('customer/session');
                    $hash[] = $customer->getCustomerGroupId();
                    break;

                case 'cart':
                    $checkout   = Mage::getSingleton('checkout/session');
                    foreach ($checkout->getQuote()->getAllItems() as $item) {
                        $hash[] = $item->getId().'/'.$item->getQty();
                    }
                    break;

                case 'compare':
                    $items = Mage::helper('catalog/product_compare')->getItemCollection();
                    foreach ($items as $item) {
                        $hash[] = $item->getId();
                    }
                    break;

                case 'wishlist':
                    $wishlistHelper = Mage::helper('wishlist');

                    if ($wishlistHelper->hasItems()) {
                        $items  = $wishlistHelper->getItemCollection();
                        foreach ($items as $item) {
                            $hash[] = $item->getId();
                        }
                    }
                    break;

                case 'product':
                    if (Mage::registry('current_product')) {
                        $hash[] = Mage::registry('current_product')->getId();
                    }
                    break;

                case 'category':
                    if (Mage::registry('current_category')) {
                        $hash[] = Mage::registry('current_category')->getId();
                    }
                    break;

                case 'store':
                    $hash[] = Mage::app()->getStore()->getCode();
                    break;

                case 'currency':
                    $hash[] = Mage::app()->getStore()->getCurrentCurrencyCode();
                    break;

                case 'locale':
                    $hash[] = Mage::app()->getLocale()->getLocaleCode();
                    break;

                case 'rotator':
                    $hash[] = 'rotator_'.rand(0, 100);
                    break;

                case 'is_home':
                    if (Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {
                        $hash[] = 'home';
                    }
                    break;

                case 'allow_save_cookies':
                    $hash[] = Mage::helper('core/cookie')->isUserNotAllowSaveCookie();
                    break;

                default:
                    break;
            }
        }

        return implode('_', $hash);
    }
}
