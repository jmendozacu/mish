<?php
class VES_VendorsPage_Block_Vendor extends Mage_Core_Block_Template
{
	protected function _prepareLayout(){
		parent::_prepareLayout();
		$block = $this->getLayout()->createBlock('vendorspage/vendor_profile','vendor.profile');
		$containerBlock = '';
		switch ($this->getProfileBlockPosition()){
			case VES_VendorsPage_Model_Source_Profile::POSITION_LEFT:
				$block->setTemplate('ves_vendorspage/vendor/profile_left.phtml');
				$containerBlock= $this->getLayout()->getBlock('vendorscms.left.top');
				if(!$containerBlock)
					$containerBlock= $this->getLayout()->getBlock('left');
				break;
			case VES_VendorsPage_Model_Source_Profile::POSITION_RIGHT:
				$containerBlock= $this->getLayout()->getBlock('vendorscms.right.top');
				if(!$containerBlock)
					$containerBlock= $this->getLayout()->getBlock('right');
				$block->setTemplate('ves_vendorspage/vendor/profile_right.phtml');
				break;
			case VES_VendorsPage_Model_Source_Profile::POSITION_CONTENT:
				$containerBlock= $this->getLayout()->getBlock('vendorscms.content.top');
				if(!$containerBlock)
					$containerBlock= $this->getLayout()->getBlock('content');
				$block->setTemplate('ves_vendorspage/vendor/profile_content.phtml');
				break;
		}
		
		$headerProfileBlock = $this->getLayout()->createBlock('core/text_list','header_profile');
		$afterLogoBlock = $this->getLayout()->createBlock('core/text_list','after_logo');
		$afterTitleBlock = $this->getLayout()->createBlock('core/text_list','after_title');
		$footerProfileBlock = $this->getLayout()->createBlock('core/text_list','footer_profile');
		$block->setChild('header_profile', $headerProfileBlock);
		$block->setChild('after_logo', $afterLogoBlock);
		$block->setChild('after_title', $afterTitleBlock);
		$block->setChild('footer_profile', $footerProfileBlock);
		
		Mage::dispatchEvent('ves_vendorspage_profile_prepare',array('profile_block'=>$block));
		$containerBlock->insert($block, '', false, 'vendors_profile_block');
	}
	
	public function getProfileBlockPosition(){
		return Mage::helper('vendorspage')->getProfileBlockPosition();
	}
}
