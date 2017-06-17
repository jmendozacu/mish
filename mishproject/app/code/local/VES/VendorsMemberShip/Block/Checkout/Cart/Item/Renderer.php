<?php

class VES_VendorsMemberShip_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer{
	public function getVendorLoginUrl(){
		return $this->getUrl('vendors');
	}
} 