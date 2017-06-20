<?php
/**
 * Adminhtml sales shipments block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsReport_Block_Vendor_Report_Sales extends Mage_Adminhtml_Block_Report_Sales_Sales
{

    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'vendor_report_sales';
        $this->_blockGroup = 'vendorsreport';
    }
}
