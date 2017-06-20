<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Helper_Template_Comment_Category extends MageWorx_SeoXTemplates_Helper_Template_Comment
{
    /**
     * Retrive comment for template edit page
     * @param int $typeId
     * @return string
     * @throws Exception
     */
    public function getComment($typeId)
    {
        $comment = '<p><p><b>' . $this->__('Available Template variables:') . '</b>';
        switch($typeId){
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_TITLE:
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_DESCRIPTION:
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_KEYWORDS:
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_DESCRIPTION:
                $comment .= '<br>' . $this->_getCategoryComment();
                $comment .= '<br>' . $this->_getCategoriesComment();
                $comment .= '<br>' . $this->_getParentCategoryComment();
                $comment .= '<br>' . $this->_getSubcategoriesComment();
                $comment .= '<br>' . $this->_getWebsiteNameComment();
                $comment .= '<br>' . $this->_getStoreNameComment();
                $comment .= '<br>' . $this->_getStoreViewNameComment();
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_SEO_NAME:
                $comment .= '<br>' . $this->_getCategoryComment();
                $comment .= '<br>' . $this->_getCategoriesComment();
                $comment .= '<br>' . $this->_getWebsiteNameComment();
                $comment .= '<br>' . $this->_getStoreNameComment();
                $comment .= '<br>' . $this->_getStoreViewNameComment();
                break;
            default:
                throw new Exception($this->__('SEO XTemplates: Unknow Category Template Type'));
        }
        return $comment;
    }

    protected function _getCategoryComment()
    {
        return '[category] - ' . $this->__('output a current category name') . ';';
    }

    protected function _getCategoriesComment()
    {
        return '[categories] - ' . $this->__('output a current categories chain starting from the first parent category and ending a current category') . ';';
    }

    protected function _getParentCategoryComment()
    {
        return '[parent_category] - ' . $this->__('output a parent category') . ';';
    }

    protected function _getSubcategoriesComment()
    {
        return '[subcategories] - ' . $this->__('output a list of subcategories for a current category') . ';';
    }

    protected function _getWebsiteNameComment()
    {
        return '[website_name] - ' . $this->__('output a current website name') . ';';
    }

    protected function _getStoreNameComment()
    {
        return '[store_name] - ' . $this->__('output a current store name') . ';';
    }

    protected function _getStoreViewNameComment()
    {
        return '[store_view_name] - ' . $this->__('output a current store view name') . ';';
    }
}