<?php
class Mirasvit_Kb_Block_Tag_List extends Mage_Core_Block_Template
{
    protected $_collection;
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('kb')->__('Tags List'));
        }
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label' => Mage::helper('kb')->__('Home'),
                'title' => Mage::helper('kb')->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ));
            $root = Mage::helper('kb')->getRootCategory();
            $breadcrumbs->addCrumb('kb'.$root->getUrlKey(), array(
                'label' => $root->getName(),
                'title' => $root->getName(),
                'link' => $root->getUrl(),
            ));
            $breadcrumbs->addCrumb('kbtagslist', array(
                'label' => $this->__('Tags'),
                'title' => $this->__('Tags'),
            ));
        }
    }

    public function getConfig() {
        return Mage::getSingleton('kb/config');
    }

    public function getTagCollection()
    {
        if (!$this->_collection) {
            $this->_collection = Mage::getModel('kb/tag')->getCollection()
                                    ->joinNotEmptyFields();
        }
        return $this->_collection;
    }

    /************************/

}