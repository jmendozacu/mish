<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.0.1
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Observer_Router
{
    /**
     * @var Bubble_CmsTree_Helper_Data
     */
    protected $_helper;

    /**
     * Initialization
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('bubble_cmstree');
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function cmsRouterMatchBefore(Varien_Event_Observer $observer)
    {
        if (!Mage::app()->getStore()->isAdmin()) {
            $suffix = $this->_helper->getUrlSuffix();
            if (strlen($suffix)) {
                $condition = $observer->getEvent()->getCondition();
                $identifier = $condition->getIdentifier();
                $lastPos = strrpos($identifier, $suffix);
                if (false !== $lastPos) {
                    $identifier = substr_replace($identifier, '', $lastPos, strlen($suffix));
                    $condition->setIdentifier($identifier);
                }
            }
        }
    }
}