<?php
class VES_VendorsMemberShip_Block_Adminhtml_Vendors extends Mage_Adminhtml_Block_Template{
	protected function _prepareLayout(){
		parent::_prepareLayout();
		$gridContainer = $this->getLayout()->getBlock('vendors');
		/*Add field for only exist vendor*/
		if($gridContainer && Mage::registry('vendors_data') && Mage::registry('vendors_data')->getId()){
			$gridBlock = $gridContainer->getChild('grid');
			$gridBlock->addColumnAfter('expiry_date', array(
				'header'    => Mage::helper('vendors')->__('Expiry Date'),
				'index'     => 'expiry_date',
				'type'		=> 'datetime',
				'width'		=> 160,
	      	),'status');
		}
	}
}
