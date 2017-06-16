<?php

/**
 * Sales report vendor controller
 *
 * @category   VES
 * @package    VES_VendorsReport
 * @author     VnEcoms Team <info@vnecoms.com>
 */
class VES_VendorsReport_Block_Vendor_Report_Sales_Bestsellers extends Mage_Adminhtml_Block_Report_Sales_Bestsellers
{
	public function __construct()
    {
        parent::__construct();
        $this->_controller = 'vendor_report_sales_bestsellers';
        $this->_blockGroup = 'vendorsreport';
    }
}
