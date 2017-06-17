<?php

/**
 * Sales report vendor controller
 *
 * @category   VES
 * @package    VES_VendorsReport
 * @author     VnEcoms Team <info@vnecoms.com>
 */
class VES_VendorsReport_Report_SalesController extends VES_VendorsReport_Controller_Abstract
{
    /**
     * Add report/sales breadcrumbs
     *
     * @return Mage_Adminhtml_Report_SalesController
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('vendorsreport')->__('Sales'), Mage::helper('vendorsreport')->__('Sales'));
        return $this;
    }

    public function salesAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_setActiveMenu('report/sales/sales')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('vendor_report_sales.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function bestsellersAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Products'))->_title($this->__('Bestsellers'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_BESTSELLERS_FLAG_CODE, 'bestsellers');

        $this->_initAction()
            ->_setActiveMenu('report/products/bestsellers')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Products Bestsellers Report'), Mage::helper('adminhtml')->__('Products Bestsellers Report'));

        $gridBlock = $this->getLayout()->getBlock('vendor_report_sales_bestsellers.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export bestsellers report grid to CSV format
     */
    public function exportBestsellersCsvAction()
    {
        $fileName   = 'bestsellers.csv';
        $grid       = $this->getLayout()->createBlock('vendorsreport/vendor_report_sales_bestsellers_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export bestsellers report grid to Excel XML format
     */
    public function exportBestsellersExcelAction()
    {
        $fileName   = 'bestsellers.xml';
        $grid       = $this->getLayout()->createBlock('vendorsreport/vendor_report_sales_bestsellers_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }


    /**
     * Export sales report grid to CSV format
     */
    public function exportSalesCsvAction()
    {
        $fileName   = 'sales.csv';
        $grid       = $this->getLayout()->createBlock('vendorsreport/vendor_report_sales_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportSalesExcelAction()
    {
        $fileName   = 'sales.xml';
        $grid       = $this->getLayout()->createBlock('vendorsreport/vendor_report_sales_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventoryreports_Adminhtml_InventoryreportsController
     */
    public function warehouseAction()
    {
        $this->_title($this->__('Inventory'))
             ->_title($this->__('Reports'))
             ->_title($this->__('Sales reports by Warehouse'));
        $this->loadLayout()
            ->_setActiveMenu('report')
            ->renderLayout();
        return $this;
    }
    
    public function warehousegridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function historyAction()
    {
        $this->_title($this->__('Inventory'))
             ->_title($this->__('Reports'))
             ->_title($this->__('Sales history reports'));
        $this->loadLayout()
            ->_setActiveMenu('report')
            ->renderLayout();
        return $this;
    }
    
    
    public function historygridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function ordersAction(){
        $this->loadLayout();
        $this->renderLayout();

    }
    public function ordersGridAction(){
        $this->loadLayout();
        $this->renderLayout();

    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('inventoryreports/adminhtml_list_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('inventoryreports/adminhtml_list_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

}
