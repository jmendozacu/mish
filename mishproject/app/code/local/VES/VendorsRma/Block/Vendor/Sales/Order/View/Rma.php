<?php
class VES_VendorsRma_Block_Vendor_Sales_Order_View_Rma
extends Mage_Adminhtml_Block_Widget_Grid_Container
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
 	public function __construct()
 	 {

    	 $this->_controller = 'vendor_sales_order_view_tab';
   		 $this->_blockGroup = 'vendorsrma';
   		 $this->_headerText = Mage::helper('vendorsrma')->__('');

         $this->_addButtonLabel = Mage::helper('vendorsrma')->__('Add New');

        $this->_addButton('request', array(
                'label'     => Mage::helper('vendorsrma')->__('Create Request from this order'),
                'onclick'   => "window.location.href='".$this->getUrl('vendors/rma_request/new',array('order_id'=>$this->getRequest()->getParam('order_id'),"package_opened"=>0))."'",
                'class'     => 'add'
        ), 0, 0, 'header', 'header');
            $this->_formScripts[] = "
            function createTicket() {
                  window.location.href='".$this->getUrl('vendors/rma_request/new',array('order_id'=>$this->getRequest()->getParam('order_id'),"package_opened"=>0))."';
            }
         "   ;

         parent::__construct();
         $this->removeButton('add');
 	 }
 	 public function getHeaderWidth(){
 	 	return 'width:0px;';
 	 }
	public function getTabLabel()
	{
		return Mage::helper('vendorsrma')->__('RMA Requests');
	}

	public function getTabTitle()
	{
		return Mage::helper('vendorsrma')->__('RMA Requests');
	}

	public function canShowTab()
	{
		if (Mage::registry('current_order')->getId()) {
			return true;
		}
		return false;
	}

	public function isHidden()
	{
		if (Mage::registry('current_order')->getId()) {
			return false;
		}
		return true;
	}

}