<?php
class VES_VendorsReport_Block_Vendor_Report_Dashboard2 extends Mage_Adminhtml_Block_Template {

    /**
     * Get reports by type
     * 
     * @param string $type
     * @return array
     */
    public function getReports($type = null) {
        return $this->helper('inventoryreports')->getReports2($type);
    }

    /**
     * Get report url
     * 
     * @param array $report
     * @return string
     */
    public function getReportUrl($type, $report) {
        $top_filter = '';
        $code = $report['code'];
        $defaultTime = $report['default_time_range'];
        $top_filter .= "select_time=$defaultTime&report_radio_select=$code";
        if(isset($report['order_status'])){
            $top_filter .= '&order_status='.$report['order_status'];
        }
        if ($code == 'order_attribute') {
            $top_filter .= '&attribute_select=shipping_method';
        }
        $top_filter = Mage::helper('inventoryplus')->base64Encode($top_filter);
        $reportUrl = $this->getUrl('vendors/report_report/'.$type, array("top_filter" => $top_filter,
            '_secure' => Mage::app()->getStore()->isCurrentlySecure()));
        return $reportUrl;
    }

}
