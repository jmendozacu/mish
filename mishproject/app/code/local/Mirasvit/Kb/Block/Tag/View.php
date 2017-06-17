<?php

class Mirasvit_Kb_Block_Tag_View extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $tag = $this->getTag();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('kb')->__('Tag %s', $tag->getName()));
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
                'link' => Mage::helper('kb/tag')->getListUrl(),
            ));
            $breadcrumbs->addCrumb('kb'.$tag->getUrlKey(), array(
                'label' => $tag->getName(),
                'title' => $tag->getName(),
            ));
        }
    }

    public function getTag()
    {
        return Mage::registry('current_tag');
    }

    /************************/

}