<?php
class Mirasvit_Kb_Block_Article_List extends Mage_Core_Block_Template
{
    protected $_collection;
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $toolbar = $this->getLayout()->createBlock('kb/article_list_toolbar', 'kb.toolbar');
        $config = Mage::getSingleton('kb/config');
        $orders = array('created_at'  => Mage::helper('kb')->__('Date'));
        if ($config->getGeneralIsRatingEnabled()) {
            $orders['rating'] = Mage::helper('kb')->__('Rating');
        }
        $toolbar->setAvailableOrders($orders)
            ->setDefaultOrder('created_at')
            ->setAvailableListModes('list')
            ;
        $toolbar->setCollection($this->getArticleCollection());
        $this->append($toolbar);
    }

    public function getCategory()
    {
        return Mage::registry('current_kbcategory');
    }

    public function getTag()
    {
        return Mage::registry('current_tag');
    }

    public function getSearchQuery()
    {
        return Mage::registry('search_query');
    }

    public function getArticleCollection()
    {
        if (!$this->collection) {
            $this->collection = Mage::getModel('kb/article')->getCollection()
                ->addFieldToFilter('main_table.is_active', true)
                ;
            if ($category = $this->getCategory()) {
                $this->collection->addCategoryIdFilter($category->getId());
            } elseif ($tag = $this->getTag()) {
                $this->collection->addTagIdFilter($tag->getId());
            } elseif ($q = $this->getSearchQuery()) {
                Mage::helper('kb')->addSearchFilter($this->collection, $q);
            }
        }
        return $this->collection;
    }

    public function isRatingEnabled()
    {
        return Mage::getSingleton('kb/config')->getGeneralIsRatingEnabled();
    }
}