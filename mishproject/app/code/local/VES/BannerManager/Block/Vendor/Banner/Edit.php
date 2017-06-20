<?php

class VES_BannerManager_Block_Vendor_Banner_Edit extends VES_BannerManager_Block_Adminhtml_Banner_Edit
{
	public function getAddBannerItemUrl($banner){
		return $this->getUrl('vendors/cms_banner_item/new',array('banner'=>$banner->getId()));
	}
}