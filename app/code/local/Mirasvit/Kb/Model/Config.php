<?php
class Mirasvit_Kb_Model_Config
{
    public function getGeneralBaseUrl()
    {
        return Mage::getStoreConfig('kb/general/base_url');
    }

    public function getGeneralIsRatingEnabled()
    {
        return Mage::getStoreConfig('kb/general/is_rating_enabled');
    }

    public function getGeneralIsUrlRewriteEnabled()
    {
        return Mage::getStoreConfig('kb/general/is_url_rewrite_enabled');
    }
}