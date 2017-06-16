<?php
class VES_VendorsReport_Model_Resource_Report_Collection extends Mage_Reports_Model_Resource_Report_Collection
{
	/**
     * Init report
     *
     * @param string $modelClass
     * @return Mage_Reports_Model_Resource_Report_Collection
     */
    public function initReport($modelClass,$vendorId)
    {
        $this->_model = Mage::getModel('vendorsreport/report')
            ->setPageSize($this->getPageSize())
            ->setStoreIds($this->getStoreIds())
            ->initCollection($modelClass,$vendorId);

        return $this;
    }
}