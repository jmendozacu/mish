<?php
class VES_VendorsReview_Block_Adminhtml_Rating extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_rating';
		$this->_blockGroup = 'vendorsreview';
		$this->_headerText = Mage::helper('vendorsreview')->__('Rating Manager');
		$this->_addButtonLabel = Mage::helper('vendorsreview')->__('New Rating');
		parent::__construct();
	}
}