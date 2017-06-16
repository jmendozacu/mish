<?php
/**
 * Adminhtml sales shipments block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsReport_Block_Vendor_Report_Product_Lowstock extends Mage_Adminhtml_Block_Report_Product_Lowstock
{

    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'vendor_report_product_lowstock';
        $this->_blockGroup = 'vendorsreport';
    }
	protected function _prepareLayout()
    {
        return Mage_Adminhtml_Block_Widget_Grid_Container::_prepareLayout();
    }
}
