<?php
class Mirasvit_Kb_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function toAdminUserOptionArray() {
        $arr = Mage::getModel('admin/user')->getCollection()->toArray();
        $result = array();
        foreach ($arr['items'] as $value) {
            $result[] = array('value'=>$value['user_id'], 'label' => $value['firstname'].' '.$value['lastname']);
        }
        return $result;
    }
    public function getAdminUserOptionArray() {
        $arr = Mage::getModel('admin/user')->getCollection()->toArray();
        $result = array();
        foreach ($arr['items'] as $value) {
            $result[$value['user_id']] = $value['firstname'].' '.$value['lastname'];
        }
        return $result;
    }

    public function getCategoriesOptionArray() {
        $collection = Mage::getModel('kb/category')->getCollection()
            ->setOrder('path', 'asc');
        $result = array();
        foreach ($collection as $category) {
            $result[$category->getId()] = str_repeat('-', $category->getLevel()).$category->getName();
        }
        return $result;
    }

    public function getVoteResult($article)
    {
        $result = Mage::getSingleton('catalog/session')->getData('kbvote'.$article->getId());
        if ($result) {
            return $result;
        }
    }

    public function markAsVoted($article, $vote)
    {
        Mage::getSingleton('catalog/session')->setData('kbvote'.$article->getId(), $vote);
    }

    public function getHomeUrl()
    {
        return $this->getRootCategory()->getUrl();
    }

    protected $_rootCategory;
    public function getRootCategory()
    {
        if (!$this->_rootCategory) {
            $this->_rootCategory = Mage::getModel('kb/category')->load(1);
        }
        return $this->_rootCategory;
    }

    public function getPagerUrl($params)
    {
        $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        if (count($params) > 0) {
            $query = http_build_query($params);
            $url .= '?'.$query;
        }
        return $url;
    }

    public function setRating($article)
    {
        if ($rating = $article->getData('rating')) {
            if ($rating > 5) {
                $rating = 5;
            }
            $article->setVotesSum($article->getVotesNum() * $rating);
        }
    }

    public function addSearchFilter($collection, $query)
    {
        $collection->getSearchInstance()->joinMatched($query, $collection, 'main_table.article_id');
    }
}