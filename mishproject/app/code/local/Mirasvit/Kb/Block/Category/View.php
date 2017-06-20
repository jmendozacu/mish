<?php

class Mirasvit_Kb_Block_Category_View extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $category = $this->getCategory();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($category->getMetaTitle()? $category->getMetaTitle() : $category->getName());
            $headBlock->setDescription($category->getMetaDescription());
            $headBlock->setKeywords($category->getMetaKeywords());
        }
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label' => Mage::helper('kb')->__('Home'),
                'title' => Mage::helper('kb')->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ));
            $ids = $category->getParentIds();
            $ids[] = 1;
            $parents = Mage::getModel('kb/category')->getCollection()
                        ->addFieldToFilter('category_id', $ids)
                        ->setOrder('level', 'asc');
            foreach ($parents as $cat) {
                $breadcrumbs->addCrumb('kb'.$cat->getUrlKey(), array(
                    'label' => $cat->getName(),
                    'title' => $cat->getName(),
                    'link' => $cat->getUrl(),
                ));
            }
            $breadcrumbs->addCrumb('kb'.$category->getUrlKey(), array(
                'label' => $category->getName(),
                'title' => $category->getName()
            ));
        }
    }

    public function getCategory()
    {
        return Mage::registry('current_kbcategory');
    }

    public function getCategoryCollection()
    {
        $collection = Mage::getModel('kb/category')->getCollection()
            ->addFieldToFilter('parent_id', $this->getCategory()->getId())
            ->addFieldToFilter('is_active', true);
        return $collection;
    }

    public function getArticleCollection($category)
    {
        $collection = Mage::getModel('kb/article')->getCollection()
            ->addCategoryIdFilter($category->getId())
            ->addFieldToFilter('main_table.is_active', true)
            ->setPageSize(2)
            ;
        return $collection;
    }

    /************************/

    public function getArticlesNumber($category)
    {
        return $category->getArticlesNumber(Mage::app()->getStore()->getId());
    }
}