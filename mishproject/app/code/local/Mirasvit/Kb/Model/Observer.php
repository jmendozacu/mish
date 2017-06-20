<?php
class Mirasvit_Kb_Model_Observer
{
    static $isRegistered;

    public function getConfig()
    {
        return Mage::getSingleton('kb/config');
    }

    public function registerUrlRewrite()
    {
        if (self::$isRegistered) {
            return;
        }
        Mage::helper('mstcore/urlrewrite')->rewriteMode('KB', $this->getConfig()->getGeneralIsUrlRewriteEnabled());
        Mage::helper('mstcore/urlrewrite')->registerBasePath('KB', $this->getConfig()->getGeneralBaseUrl());
        Mage::helper('mstcore/urlrewrite')->registerPath('KB', 'ARTICLE', '[category_key]/[article_key]', 'kb_article_view');
        Mage::helper('mstcore/urlrewrite')->registerPath('KB', 'CATEGORY', '[category_key]', 'kb_category_view');
        Mage::helper('mstcore/urlrewrite')->registerPath('KB', 'TAG', 'tags/[tag_key]', 'kb_tag_view');
        Mage::helper('mstcore/urlrewrite')->registerPath('KB', 'TAG_LIST', 'tags', 'kb_tag_index');

        self::$isRegistered = true;
    }
}