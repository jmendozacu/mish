<?php
class Mirasvit_Kb_Model_Resource_Article_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('kb/article', 'article_id');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('article_id', 'name');
    }

    public function getOptionArray()
    {
        $arr = array();
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }
        return $arr;
    }


    public function addStoreIdFilter($storeId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('kb/article_store')}`
                AS `article_store_table`
                WHERE main_table.article_id = article_store_table.as_article_id
                AND article_store_table.as_store_id in (?))", array(0, $storeId));
        return $this;
    }

    public function addCategoryIdFilter($categoryId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('kb/article_category')}`
                AS `article_category_table`
                WHERE main_table.article_id = article_category_table.ac_article_id
                AND article_category_table.ac_category_id in (?))", array(0, $categoryId));
        return $this;
    }

    public function addTagIdFilter($tagId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('kb/article_tag')}`
                AS `article_tag_table`
                WHERE main_table.article_id = article_tag_table.at_article_id
                AND article_tag_table.at_tag_id in (?))", array(0, $tagId));
        return $this;
    }

    protected function initFields()
    {
        $select = $this->getSelect();
        $select->joinLeft(array('user' => $this->getTable('admin/user')), 'main_table.user_id = user.user_id', array('user_name' => 'CONCAT(firstname, " ", lastname)'));
        $select->columns(array('rating' => new Zend_Db_Expr("votes_sum/votes_num")));
    }

    protected function _initSelect()
    {
        $this->initFields();
        parent::_initSelect();
    }

    public function getSearchInstance()
    {
        $collection = Mage::getModel('kb/article')->getCollection();

        $search = Mage::getModel('kb/search');
        $search->setSearchableCollection($collection);
        $search->setSearchableAttributes(array(
            'main_table.article_id' => 0,
            'main_table.name' => 100,
            'main_table.text' => 50,
            'main_table.meta_title' => 80,
            'main_table.meta_keywords' => 80,
            'main_table.meta_description' => 60,
        ));
        $search->setPrimaryKey('article_id');

        return $search;
    }
}