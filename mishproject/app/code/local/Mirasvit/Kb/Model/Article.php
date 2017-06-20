<?php
class Mirasvit_Kb_Model_Article extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('kb/article');
    }

    public function getUrl() {
    	return Mage::helper('mstcore/urlrewrite')->getUrl('KB', 'ARTICLE', $this);
    }

    public function getCategoryIds()
    {
        $ids = $this->getData('category_ids');
        if (count($ids)) {
            return $ids;
        } else {
            return array(1);
        }
    }

    public function getCategory() {
        $ids = $this->getCategoryIds();
        $categoryId = $ids[0];
        $category = Mage::getModel('kb/category')->load($categoryId);
        return $category;
    }

    public function getCategories()
    {
        $collection = Mage::getModel('kb/category')->getCollection()
                        ->addFieldToFilter('category_id', $this->getCategoryIds());
        return $collection;
    }

    public function getTags()
    {
        $collection = Mage::getModel('kb/tag')->getCollection()
                        ->addFieldToFilter('tag_id', $this->getTagIds());
        return $collection;
    }

    public function getUser()
    {
        $user = Mage::getModel('admin/user')->load($this->getUserId());
        return $user;
    }

    public function getRating()
    {
        if ($this->getVotesNum()) {
            return $this->getVotesSum()/$this->getVotesNum();
        }
    }

    public function addVote($vote)
    {
        $this->setVotesNum($this->getVotesNum() + 1)
             ->setVotesSum($this->getVotesSum() + $vote);
        return $this;
    }

    public function getPostiveVoteNum()
    {
        $s1 = $this->getVotesSum();
        $s2 = $this->getVotesNum();
        return $s2 - (5*$s2 - $s1)/4;
    }
}