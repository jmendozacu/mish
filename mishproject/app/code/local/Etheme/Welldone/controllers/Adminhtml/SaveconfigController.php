<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Adminhtml_SaveconfigController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
        $this->loadLayout();
        $this->_addLeft($this->getLayout()->createBlock('core/text', 'leftside')->setText('<h2>Theme Maintenance</h2><h4>Save your configuration from choosen store to xml file (as preset  Configset). Your saved configuration after can be loaded in menu "Load preset configuration".</h4>'));
        $this->_addContent($this->getLayout()->createBlock('welldone/adminhtml_saveconfig_edit'));
        $this->_setActiveMenu('etheme');
        $this->renderLayout();
	}

    public function saveconfigAction()
    {

        require_once(Mage::getBaseDir().'/app/code/local/Etheme/Welldone/lib/Array2Xml.php');
        $name=$this->getRequest()->getParam('name');
        $store=$this->getRequest()->getParam('store');
        $scope = $store ? 'stores' : 'default';

        $sections=array('welldone','welldonelayout','welldonecolors','web','design','ajax_shop','ajax_shop','ajax_addto');

        $data=array();
        $data['name']=$name;

        foreach($sections as $section)
        {
            $data['sections'][$section]=Mage::getStoreConfig($section,$store);
        }

        $xml = Array2XML::createXML('config', $data);

        $file = Mage::getBaseDir().'/app/code/local/Etheme/Welldone/etc/import/configsets/'.$data['name'].'.xml';
        $content = $xml->saveXML();
        @file_put_contents($file, $content);

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('welldone')->__('New preset configuration saved successfully. You can use it from "Load preset configuration" and find in folder '.$file));
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }
}



