<?php

class Mirasvit_Kb_Block_Article_View extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $article = $this->getArticle();
        $category = $article->getCategory();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($article->getMetaTitle()? $article->getMetaTitle() : $article->getName());
            $headBlock->setDescription($article->getMetaDescription());
            $headBlock->setKeywords($article->getMetaKeywords());
        }
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label' => Mage::helper('kb')->__('Home'),
                'title' => Mage::helper('kb')->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ));
            $ids = array(0);
            if (is_array($category->getParentIds())) {
                $ids = array_merge($ids, $category->getParentIds());
            }
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
                'title' => $category->getName(),
                'link' => $category->getUrl(),
            ));
            $breadcrumbs->addCrumb('kb'.$article->getUrlKey(), array(
                'label' => $article->getName(),
                'title' => $article->getName(),
            ));
        }
     }

    public function getArticle()
    {
        return Mage::registry('current_article');
    }

    public function getCategories()
    {
        $collection = $this->getArticle()->getCategories()
            ->addFieldToFilter('is_active', true);
        return $collection;
    }
    public function getTags()
    {
        $collection = $this->getArticle()->getTags();
        return $collection;
    }
    public function getVoteUrl($vote)
    {
        return Mage::getUrl('*/*/vote', array('id'=>$this->getArticle()->getId(), 'vote' => $vote));
    }
    public function getVoteResult()
    {
        return Mage::helper('kb')->getVoteResult($this->getArticle());
    }
    public function isRatingEnabled()
    {
        return Mage::getSingleton('kb/config')->getGeneralIsRatingEnabled();
    }
}