<?php
class Mirasvit_Kb_Block_Adminhtml_Category_Tree extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{

    protected $_withProductCount;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mst_kb/category/tree.phtml');
        $this->setUseAjax(true);
        $this->_withProductCount = true;
    }

    protected function _prepareLayout()
    {
        $addCategoryUrl = $this->getUrl("*/*/add", array(
            '_current'    => true,
            'category_id' => null,
            '_query'      => false
        ));

        $this->setChild('add_category_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('kb')->__('Add Category'),
                    'onclick'   => "addNew('".$addCategoryUrl."', false)",
                    'class'     => 'add',
                    'id'        => 'add_category_button'
                ))
        );

        return parent::_prepareLayout();
    }

    public function getCategory()
    {
        return Mage::getModel('kb/category')->load(1);
    }

    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current' => true, 'id' => null,'store' => null);
        if (
            (is_null($expanded) && Mage::getSingleton('admin/session')->getIsTreeWasExpanded())
            || $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/categoriesJson', $params);
    }

    public function getNodesUrl()
    {
        return $this->getUrl('*/*/jsonTree');
    }

    public function getMoveUrl()
    {
        return $this->getUrl('*/adminhtml_category/move');
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/adminhtml_category/edit');
    }

    public function getTree()
    {
        $rootArray = $this->_getNodeJson($this->getCategory());
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    public function getTreeJson($parenNodeCategory = null)
    {
        if ($parenNodeCategory == null) {
            $parenNodeCategory = $this->getCategory();
        }
        $rootArray = $this->_getNodeJson($parenNodeCategory);
        $json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }
        $items = array();
        $ids   = explode('/', $path);

        foreach ($ids as $id) {
            $item        = Mage::getModel('kb/category')->load($id);
            $items[] = $this->_getNodeJson($item);
        }
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($items) . ';'
            . '</script>';
    }

    protected function _getNodeJson($node)
    {
        $item = array(
            'id'        => $node->getId(),
            'path'      => $node->getPath(),
            'cls'       => ($node->getIsActive() ? 'active' : 'no-active'),
            'text'      => $this->_buildNodeName($node),
            'allowDrag' => true,
            'allowDrop' => true,
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

    protected function _buildNodeName($node)
    {
        $name = '';
        // $name .= $node->getPosition().'|'.$node->getLevel().'|'.$node->getPath().'|';

        $name .= $node->getName();

        return $name;
    }
}
