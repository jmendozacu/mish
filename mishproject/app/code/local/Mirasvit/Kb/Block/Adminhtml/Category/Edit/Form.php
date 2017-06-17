<?php
class Mirasvit_Kb_Block_Adminhtml_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mst_kb/category/edit/form.phtml');
    }

    protected function _prepareLayout()
    {
        $itemId = $this->getCategoryId();

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('kb')->__('Save Category'),
                    'onclick' => "itemSubmit('" . $this->getSaveUrl() . "', true)",
                    'class'   => 'save'
                ))
        );

        if ($itemId) {
            $this->setChild('delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'   => Mage::helper('kb')->__('Delete Category'),
                        'onclick' => "itemDelete('" . $this->getUrl('*/*/delete', array('_current' => true)) . "', true, {$itemId})",
                        'class'   => 'delete'
                    ))
            );
        }

        $this->setChild('tabs',
            $this->getLayout()->createBlock('kb/adminhtml_category_edit_tabs', 'tabs'.rand(0,10))
        );

        return parent::_prepareLayout();
    }

    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    public function getCategory()
    {
        return Mage::registry('current_model');
    }

    public function getCategoryId()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }
    }

    public function getHeader()
    {
        if ($this->getCategoryId()) {
            return $this->getCategory()->getName();
        } else {
            return Mage::helper('kb')->__('New Category');
        }
    }

    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);

        return $this->getUrl('*/*/delete', $params);
    }

    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);

        return $this->getUrl('*/*/save', $params);
    }

    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }

    /************************/

}