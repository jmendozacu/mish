<?php
class Mirasvit_Kb_Block_Search_Result extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $category = $this->getCategory();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle('Search Results');
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
            $breadcrumbs->addCrumb('kbsearchlist', array(
                'label' => $this->__('Search Results'),
                'title' => $this->__('Search Results'),
            ));
        }
    }

	public function getQuery()
	{
		return Mage::registry('search_query');
	}
}