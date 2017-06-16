<?php

class VES_VendorsCategory_Block_Vendor_Category_Edit_Form extends VES_VendorsCategory_Block_Vendor_Category_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendorscategory/vendor/category/edit/form.phtml');
    }

    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }

    protected function _prepareLayout()
    {
        $category = $this->getCategory();
        $categoryId = (int) $category->getId(); // 0 when we create category, otherwise some value for editing category


        $this->setChild('tabs',
            $this->getLayout()->createBlock('vendorscategory/vendor_category_tabs', 'tabs')
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Save Category'),
                    'onclick'   => "categorySubmit('" . $this->getSaveUrl() . "', true)",
                    'class' => 'save'
                ))
        );

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Delete Category'),
                    'onclick'   => "categoryDelete('" . $this->getUrl('*/*/delete', array('_current' => true)) . "', true, {$categoryId})",
                    'class' => 'delete'
                ))
        );

        $resetPath = $categoryId ? '*/*/edit' : '*/*/add';
        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Reset'),
                    'onclick'   => "categoryReset('".$this->getUrl($resetPath, array('_current'=>true))."',true)"
                ))
        );


        return parent::_prepareLayout();
    }

    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getSaveButtonHtml()
    {

            return $this->getChildHtml('save_button');

    }

    public function getResetButtonHtml()
    {
            return $this->getChildHtml('reset_button');

    }

    public function getHeader()
    {

            if ($this->getCategoryId()) {
                return $this->getCategoryName();
            } else {
                $parentId = (int) $this->getRequest()->getParam('parent');
                if ($parentId) {
                    return Mage::helper('catalog')->__('New Subcategory');
                } else {
                    return Mage::helper('catalog')->__('New Category');
                }
            }

        return Mage::helper('catalog')->__('Set Root Category for Store');
    }

    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * Return URL for refresh input element 'path' in form
     *
     * @param array $args
     * @return string
     */
    public function getRefreshPathUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }
}