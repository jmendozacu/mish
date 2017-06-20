<?php
class VES_VendorsCms_Block_Page extends Mage_Core_Block_Abstract
{
    /**
     * Retrieve Page instance
     *
     * @return Mage_Cms_Model_Page
     */
    public function getPage()
    {
        if (!$this->hasData('page')) {
            $this->setData('page', Mage::registry('vendorscms_page'));
        }
        return $this->getData('page');
    }

    /**
     * Prepare global layout
     *
     * @return Mage_Cms_Block_Page
     */
    protected function _prepareLayout()
    {
        $page = $this->getPage();
        // show breadcrumbs
        if (Mage::helper('vendorscms')->showBreadCrumbs()
            && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))
            && ($page->getIdentifier()!==Mage::helper('vendorscms')->getDefaultCmsHomePage())) {
                $breadcrumbs->addCrumb('home', array('label'=>Mage::registry('vendor')->getTitle(), 'title'=>Mage::helper('vendorscms')->__('Go to Home Page'), 'link'=>$this->getUrl().Mage::getStoreConfig('vendors/vendor_page/url_key').'/'.Mage::registry('vendor_id')));
                $breadcrumbs->addCrumb('cms_page', array('label'=>$page->getTitle(), 'title'=>$page->getTitle()));
        }

        $root = $this->getLayout()->getBlock('root');
        if ($root) {
            $root->addBodyClass('cms-'.$page->getIdentifier());
            /*Set root template*/
            switch($page->getRootTemplate()){
            	case 'empty';
            		$template = 'empty.phtml';
            		break;
            	case 'one_column';
            		$template = '1column.phtml';
            		break;
            	case 'two_columns_left';
            		$template = '2columns-left.phtml';
            		break;
            	case 'two_columns_right';
            		$template = '2columns-right.phtml';
            		break;
            	case 'three_columns';
            		$template = '3columns.phtml';
            		break;
            }
            $root->setTemplate('page/'.$template);
        }

        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->setTitle($page->getTitle());
            $head->setKeywords($page->getMetaKeywords());
            $head->setDescription($page->getMetaDescription());
        }
        return parent::_prepareLayout();
    }

    /**
     * Prepare HTML content
     *
     * @return string
     */
    protected function _toHtml()
    {
        /* @var $helper Mage_Cms_Helper_Data */
        $processor = Mage::getModel('vendorscms/template_filter');
        $html = $processor->filter($this->getPage()->getContent());
        $html = $this->getMessagesBlock()->toHtml() . $html;
        return $html;
    }
}
