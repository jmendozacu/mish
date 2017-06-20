<?php
/**
 * Adminhtml sales shipments block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsReport_Block_Vendor_Report_Product_Sold extends Mage_Adminhtml_Block_Report_Product_Sold
{

    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'vendor_report_product_sold';
        $this->_blockGroup = 'vendorsreport';
    }
}
