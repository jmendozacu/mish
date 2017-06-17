<?php
class Mirasvit_Kb_Block_Adminhtml_Article_Edit_Tab_Categories extends Mirasvit_Kb_Block_Adminhtml_Category_Tree
{
    protected $_categoryIds;
    protected $_selectedNodes = null;

    /**
     * Specify template to use
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mst_kb/article/edit/categories.phtml');
    }

    public function getArticle()
    {
        return Mage::registry('current_article');
    }

    public function isReadonly()
    {
        return false;
    }

    protected function getCategoryIds()
    {
        if ($ids = $this->getArticle()->getCategoryIds()) {
            return $ids;
        } else {
            return array();
        }
    }

    public function getIdsString()
    {
        return implode(',', $this->getCategoryIds());
    }

    public function getLoadTreeUrl($expanded = null)
    {
        return $this->getUrl('*/*/categoriesJson', array('_current' => true));
    }

    protected function _getNodeJson($node)
    {
        $item = array(
            'id'      => $node->getId(),
            'path'    => $node->getPath(),
            'cls'     => ($node->getIsActive() ? 'active' : 'no-active'),
            'text'    => $this->_buildNodeName($node),
            'checked' => in_array($node->getId(), $this->getCategoryIds()),
        );

        if ($node->hasChildren()) {
            $item['children'] = array();
            foreach ($node->getChildren() as $child) {
                $item['children'][] = $this->_getNodeJson($child);
            }
            $item['expanded'] = true;
        }

        return $item;
    }
}
