<?php
/**
 * Adminhtml sales shipments block
 *
 * @category   	VES
 * @package    	VES_Vendors
 * @author    	Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsReport_Block_Vendor_Report_Product_Sold_Grid extends Mage_Adminhtml_Block_Report_Product_Sold_Grid
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->unsetChild('store_switcher');
        return $this;
    }
    
	/**
     * Prepare collection object for grid
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
	protected function _prepareCollection()
    {
        $filter = $this->getParam($this->getVarNameFilter(), null);

        if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }

        if (is_string($filter)) {
            $data = array();
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);

            if (!isset($data['report_from'])) {
                // getting all reports from 2001 year
                $date = new Zend_Date(mktime(0,0,0,1,1,2001));
                $data['report_from'] = $date->toString($this->getLocale()->getDateFormat('short'));
            }

            if (!isset($data['report_to'])) {
                // getting all reports from 2001 year
                $date = new Zend_Date();
                $data['report_to'] = $date->toString($this->getLocale()->getDateFormat('short'));
            }

            $this->_setFilterValues($data);
        } else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        } else if(0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }
        /** @var $collection Mage_Reports_Model_Resource_Report_Collection */
        $collection = Mage::getResourceModel('vendorsreport/report_collection');

        $collection->setPeriod($this->getFilter('report_period'));

        if ($this->getFilter('report_from') && $this->getFilter('report_to')) {
            /**
             * Validate from and to date
             */
            try {
                $from = $this->getLocale()->date($this->getFilter('report_from'), Zend_Date::DATE_SHORT, null, false);
                $to   = $this->getLocale()->date($this->getFilter('report_to'), Zend_Date::DATE_SHORT, null, false);

                $collection->setInterval($from, $to);
            }
            catch (Exception $e) {
                $this->_errors[] = Mage::helper('reports')->__('Invalid date specified.');
            }
        }

        /**
         * Getting and saving store ids for website & group
         */
        $storeIds = array();
        if ($this->getRequest()->getParam('store')) {
            $storeIds = array($this->getParam('store'));
        } elseif ($this->getRequest()->getParam('website')){
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        } elseif ($this->getRequest()->getParam('group')){
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
        }

        // By default storeIds array contains only allowed stores
        $allowedStoreIds = array_keys(Mage::app()->getStores());
        // And then array_intersect with post data for prevent unauthorized stores reports
        $storeIds = array_intersect($allowedStoreIds, $storeIds);
        // If selected all websites or unauthorized stores use only allowed
        if (empty($storeIds)) {
            $storeIds = $allowedStoreIds;
        }
        // reset array keys
        $storeIds = array_values($storeIds);


        $collection->setStoreIds($storeIds);

        if ($this->getSubReportSize() !== null) {
            $collection->setPageSize($this->getSubReportSize());
        }
		$collection->initReport('vendorsreport/product_sold_collection',Mage::getSingleton('vendors/session')->getVendor()->getId());
        $this->setCollection($collection);

        Mage::dispatchEvent('adminhtml_widget_grid_filter_collection',
                array('collection' => $this->getCollection(), 'filter_values' => $this->_filterValues)
        );
    }
}
