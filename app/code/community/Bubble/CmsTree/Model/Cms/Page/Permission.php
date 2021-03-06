<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.0.1
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Cms_Page_Permission extends Mage_Core_Model_Abstract
{
    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_init('cms/page_permission');
    }
}