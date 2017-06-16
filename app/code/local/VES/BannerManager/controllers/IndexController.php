<?php
class VES_BannerManager_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	//echo "<img src=\"". Mage::helper('bannermanager/image')->init('ves_checkoutuploadimages/cmnd_sau.jpg')->resize(135)."\">";
    	//$block = $this->getLayout()->createBlock('bannermanager/banner','banner1');
    	//$block->setBannerId('banner1');
    	//echo $block->toHtml();
    	$this->loadLayout()->renderLayout();
    }
}