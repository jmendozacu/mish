<?php
class VES_VendorsReport_Model_Report extends Mage_Reports_Model_Report
{
	public function initCollection($modelClass,$vendorId)
    {
        $this->_reportModel = Mage::getResourceModel($modelClass);
		$this->_reportModel->setVendorId($vendorId);
        return $this;
    }
}