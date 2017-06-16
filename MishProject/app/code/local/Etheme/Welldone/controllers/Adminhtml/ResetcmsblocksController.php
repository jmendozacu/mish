<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Adminhtml_ResetcmsblocksController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{

        $this->loadLayout();
        $this->_addLeft($this->getLayout()->createBlock('core/text', 'leftside')->setText('<h2>Theme Maintenance</h2><h4>Add description later.</h4>'));
        $this->_addContent($this->getLayout()->createBlock('welldone/adminhtml_resetcmsblocks_edit'));
        $this->_setActiveMenu('etheme');
        $this->renderLayout();
	}

    public function resetAction(){
        $rewrite_deleted = $this->getRequest()->getParam('cms_rewrite_no', 0);
        $flag=false;
        if($rewrite_deleted)
        {
            $flag=true;
        }

        Mage::getModel('welldone/content')->installTemplateResources($flag);
        Mage::getModel('welldone/content')->installTemplateBlocks($flag);
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('welldone')->__('CMS blocks/pages has been restored to default. If you do not see the changes please clean the cache.'));
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

    protected function _resetNode($xpath)
    {
        $store = $this->getRequest()->getParam('store', 0);
        $scope = $store ? 'stores' : 'default';
        $tpl_settings_def = new Varien_Simplexml_Config();
        $tpl_settings_def->loadFile(Mage::getBaseDir().'/app/code/local/Etheme/Welldone/etc/config.xml');
        $sets=$tpl_settings_def->getNode('default/welldone/welldone_'.$xpath)->children();
        foreach ($sets as $item) Mage::getConfig()->saveConfig('welldone/welldone_'.$xpath.'/'.$item->getName(), (string)$item, $scope, $store);
    }
}



