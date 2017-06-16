<?php
class VES_VendorsRma_Block_Vendor_Sales_Order_View_Tab_Grid
extends VES_VendorsRma_Block_Vendor_Request_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('requestGrid');
		$this->setDefaultSort('created_at');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		//$this->setFilterVisibility(false);
		//$this->setPagerVisibility(false);
	}
	
	protected function _prepareCollection()
	{
		$id = Mage::registry('current_order')->getData('increment_id');
		$collection = Mage::getModel('vendorsrma/request')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('order_incremental_id',array('eq'=>$id))->addAttributeToFilter('increment_id',array('neq'=>null));
        Mage::dispatchEvent("vendor_rma_grid_prepare_colletion_before", array("collection" => $collection));
        $this->setCollection($collection);
		return parent::_prepareCollection();
	}

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->removeColumn("action");
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('vendors/rma_request/edit', array('id' => $row->getId()));
    }
    public function getGridUrl(){
        return $this->getCurrentUrl(array('active_tab'=>'rma'));
    }

}