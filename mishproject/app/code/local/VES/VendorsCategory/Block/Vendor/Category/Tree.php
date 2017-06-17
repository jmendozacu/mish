<?php
class VES_VendorsCategory_Block_Vendor_Category_Tree extends VES_VendorsCategory_Block_Vendor_Category_Abstract {

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendorscategory/vendor/category/tree.phtml');
        $this->setUseAjax(true);
    }

    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl("*/*/add", array(
            '_current'=>true,
            'id'=>null,
            '_query' => false
        ));

        $this->setChild('add_sub_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add Subcategory'),
                    'onclick'   => "addNew('".$addUrl."', false)",
                    'class'     => 'add',
                    'id'        => 'add_subcategory_button',
                ))
        );

        $this->setChild('add_root_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add New Category'),
                    'onclick'   => "addNew('".$addUrl."', true)",
                    'class'     => 'add',
                    'id'        => 'add_root_category_button'
            ))
        );

        return parent::_prepareLayout();
    }

    public function getAddSubButtonHtml() {
        return $this->getChildHtml('add_sub_button');
    }

    public function getAddRootButtonHtml() {
        return $this->getChildHtml('add_root_button');
    }

    public function canAddRootCategory()
    {
        return true;
    }

    public function canAddSubCategory() {
        return true;
    }

    /**
     * Check if page loaded by outside link to category edit
     *
     * @return boolean
     */
    public function isClearEdit()
    {
        return (bool) $this->getRequest()->getParam('clear');
    }

    public function getProduct() {
        return Mage::registry('current_product');
    }

    public function getIdsString() {
        if($this->getProduct()) {
            return $this->getProduct()->getData('vendor_categories');
        }
        return '';
    }

    /**
     * Get Json Representation of category Tree
     *
     * @return string
     */
    public function getResTreeJson()
    {
        $vendor = Mage::getSingleton('vendors/session')->getVendor();
        $rootCategory = Mage::getModel('vendorscategory/category')->getCollection()->addFieldToFilter('vendor_id',$vendor->getId())
                        ->addFieldToFilter('level',0)
        ;
        $result = array('id'=>'0','level'=>'-1','text'=>'Root');
        foreach($rootCategory as $_root) {
            $data = $_root->getData();
            $data['text'] = $_root->getName();
            $data['id'] = $_root->getId();
            $data['cls'] = 'folder ' . ($_root->getIsActive() ? 'active-category' : 'no-active-category');
            $data['children'] = $this->_prepareData($_root);
            if($this->getIdsString()) {
                Mage::log($this->getIdsString());
                if(in_array($_root->getId(), explode(',',$this->getIdsString()))) {$data['checked'] = true;}
                else {$data['checked'] = false;}
            }
            $result['children'][] = $data;
        }
        if (!empty($result['children'])) {
            usort($result['children'], array($this, '_sortTree'));
        }

        $json = Mage::helper('core')->jsonEncode(isset($result['children']) ? $result['children'] : array());
        return $json;
    }

    public function getTreeJson($category) {
        $parentCategory = Mage::getModel('vendorscategory/category')->load($category->getData('parent_category_id'));

        $childrens = Mage::getModel('vendorscategory/category')->getCollection()
            ->addFieldToFilter('parent_category_id',$parentCategory->getId());

        $result = array();
        foreach($childrens as $child) {
            $data = $child->getData();
            $data['text'] = $child->getName();
            $data['id'] = $child->getId();
            $data['cls'] = 'folder ' . ($child->getIsActive() ? 'active-category' : 'no-active-category');
            $data['children'] = array();
            $result[] = $data;
        }

        $json = Mage::helper('core')->jsonEncode(isset($result['children']) ? $result['children'] : array());
        return $json;

    }

    protected function _prepareData($_category){
        $result = array();
        $categoryCollection = $_category->getChildrenCategoryCollection(false);
        foreach($categoryCollection as $_subCat) {
            $data = $_subCat->getData();
            $data['text'] = $_subCat->getName();
            $data['id'] = $_subCat->getId();
            if($this->getIdsString()) {
                if(in_array($_subCat->getId(), explode(',',$this->getIdsString()))) {$data['checked'] = true;}
                else {$data['checked'] = false;}
            }
            $data['cls'] = 'folder ' . ($_subCat->getIsActive() ? 'active-category' : 'no-active-category');
            $data['children'] = $this->_prepareData($_subCat);

            $result[] = $data;
        }

        return $result;
    }

    /**
     * Compare two nodes of the Resource Tree
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortTree($a, $b)
    {
        return $a['sort_order']<$b['sort_order'] ? -1 : ($a['sort_order']>$b['sort_order'] ? 1 : 0);
    }

    /**
     * Get JSON of array of categories, that are breadcrumbs for specified category path
     *
     * @param string $path
     * @param string $javascriptVarName
     * @return string
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if(empty($path)) {
            return '';
        }
        $categories = array();
        $category_ids = explode('/',$path);
        foreach($category_ids as $id) {
            $cat = Mage::getModel('vendorscategory/category')->load($id);
            $data = $cat->getData();
            $data['children'] = array();
            $data['text'] = $cat->getName();
            $data['id'] = $cat->getId();
            $data['cls'] = 'folder ' . ($cat->getIsActive() ? 'active-category' : 'no-active-category');

            $categories[] = $data;
        }
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($categories) . ';'
            . '</script>';
    }

    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current'=>true, 'id'=>null,'store'=>null);
        if (
            (is_null($expanded) && Mage::getSingleton('vendors/session')->getIsTreeWasExpanded())
            || $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/categoriesJson', $params);
    }

    public function getIsWasExpanded()
    {
        return Mage::getSingleton('vendors/session')->getIsTreeWasExpanded();
    }

    public function getMoveUrl()
    {
        return $this->getUrl('*/catalog_category/move', array('store'=>$this->getRequest()->getParam('store')));
    }
}