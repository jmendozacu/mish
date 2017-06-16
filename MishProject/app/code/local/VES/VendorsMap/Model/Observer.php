<?php
class VES_VendorsMap_Model_Observer 
{
	public function appendTabAccount(Varien_Event_Observer $observer){
        $tab = $observer->getData('tabs');
        if(Mage::registry("vendors_data") && Mage::app()->getRequest()->getParam("id")) {
            $tab->setActiveTab("info_section");
        }
        /*
		$block = $observer->getEvent()->getBlock();
		if (!isset($block)) {
			return $this;
		}
		if ($block->getType() == 'vendors/account_edit_tabs' )
		{
			$blocks = Mage::getSingleton('core/layout');
			//var_dump($block->getTabsIds()); exit;
			$block->addTabAfter('map_section', array(
					'label'     => Mage::helper('vendorsmap')->__('Map'),
					'title'     => Mage::helper('vendorsmap')->__('Map'),
					'content'   => $blocks->createBlock('vendorsmap/vendor_account_map')->toHtml(),
			),"main_section");
			//$block->removeTab("main_section");
		}
        */
	}
	
	public function ves_vendorspage_map_prepare(Varien_Event_Observer $observer){
		$profileBlock = $observer->getProfileBlock();
		$ratingBlock = $profileBlock->getLayout()->createBlock('vendorsmap/map_sidebar','vendor.map')->setTemplate('ves_vendorsmap/map/sidebar.phtml');
		$footerProfile = $profileBlock->getChild('footer_profile');
		$footerProfile->insert($ratingBlock, '', false, 'vendors_map_block');
	}
	
	public function appendFieldSetAccount($observer){
        $tab = $observer->getData('tab');
      //  var_dump($tab);exit
        if (!Mage::app()->getStore()->isAdmin()) {
            $form = $observer->getData('form');
            $blocks = Mage::getSingleton('core/layout');
            $fieldset = $form->addFieldset('vendors_map', array('legend'=>Mage::helper('vendors')->__('Map')));
            $map=$fieldset->addField('map', 'text', array(
                    'value'  => 'map',
                    'tabindex' => 1
            ));
            $map->setRenderer($blocks->createBlock('vendorsmap/vendor_account_map'));
        }
        else if(Mage::registry("vendors_data") && Mage::app()->getRequest()->getParam("id")){
            $tab->setData("active",true);
            $form = $observer->getData('form');
            $blocks = Mage::getSingleton('core/layout');
            $fieldset = $form->addFieldset('vendors_map', array('legend'=>Mage::helper('vendors')->__('Map')));
            $map=$fieldset->addField('map', 'text', array(
                'value'  => 'map',
                'tabindex' => 1
            ));
            $map->setRenderer($blocks->createBlock('vendorsmap/adminhtml_vendor_account_map'));
        }
	}
}