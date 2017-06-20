<?php
class VES_VendorsReport_Model_Resource_Product_Sold_Collection extends Mage_Reports_Model_Resource_Product_Sold_Collection
{
	protected $_vendor_id =0;
	/**
     * Set Vendor Id
     *
     * @param string $vendorId
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function setVendorId($vendorId)
    {
        $this->_vendor_id = $vendorId;
        return $this;
    }
	
	/**
     * Set Date range to collection
     *
     * @param int $from
     * @param int $to
     * @return Mage_Reports_Model_Resource_Product_Sold_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addOrderedQty($from, $to)
            ->setOrder('ordered_qty', self::SORT_ORDER_DESC);
		$this->_select->where('order.vendor_id=?',$this->_vendor_id);
        return $this;
    }
}