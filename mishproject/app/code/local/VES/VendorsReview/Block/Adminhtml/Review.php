<?php
class VES_VendorsReview_Block_Adminhtml_Review extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_review';
		$this->_blockGroup = 'vendorsreview';
		$this->_headerText = Mage::helper('vendorsreview')->__('Reviews Manager');
		$this->_addButtonLabel = Mage::helper('vendorsreview')->__('New Review');
		parent::__construct();
		$this->_removeButton('add');
		
	}
}