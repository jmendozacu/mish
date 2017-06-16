<?php

/**
 * Sales report vendor controller
 *
 * @category   VES
 * @package    VES_VendorsReport
 * @author     VnEcoms Team <info@vnecoms.com>
 */
class VES_VendorsReport_Report_ProductController extends VES_VendorsReport_Controller_Abstract
{
	/**
     * Add report/products breadcrumbs
     *
     * @return Mage_Adminhtml_Report_ProductController
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('reports')->__('Products'), Mage::helper('reports')->__('Products'));
        return $this;
    }
	/**
     * Low stock action
     *
     */
    public function lowstockAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Low Stock'));

        $this->_initAction()
            ->_setActiveMenu('report/product/lowstock')
            ->_addBreadcrumb(Mage::helper('reports')->__('Low Stock'), Mage::helper('reports')->__('Low Stock'))
            ->_addContent($this->getLayout()->createBlock('vendorsreport/vendor_report_product_lowstock'))
            ->renderLayout();
    }

    /**
     * Export low stock products report to CSV format
     *
     */
    public function exportLowstockCsvAction()
    {
        $fileName   = 'products_lowstock.csv';
        $content    = $this->getLayout()->createBlock('vendorsreport/vendor_report_product_lowstock_grid')
            ->setSaveParametersInSession(true)
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export low stock products report to XML format
     *
     */
    public function exportLowstockExcelAction()
    {
        $fileName   = 'products_lowstock.xml';
        $content    = $this->getLayout()->createBlock('vendorsreport/vendor_report_product_lowstock_grid')
            ->setSaveParametersInSession(true)
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    
    
	/**
     * Most viewed products
     *
     */
    public function viewedAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Products'))->_title($this->__('Most Viewed'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE, 'viewed');

        $this->_initAction()
            ->_setActiveMenu('report/products/viewed')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Products Most Viewed Report'), Mage::helper('adminhtml')->__('Products Most Viewed Report'));

        $gridBlock = $this->getLayout()->getBlock('vendor_report_product_viewed.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export products most viewed report to CSV format
     *
     */
    public function exportViewedCsvAction()
    {
        $fileName   = 'products_mostviewed.csv';
        $grid       = $this->getLayout()->createBlock('vendorsreport/vendor_report_product_viewed_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export products most viewed report to XML format
     *
     */
    public function exportViewedExcelAction()
    {
        $fileName   = 'products_mostviewed.xml';
        $grid       = $this->getLayout()->createBlock('vendorsreport/vendor_report_product_viewed_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
	/**
     * Sold Products Report Action
     *
     */
    public function soldAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Products Ordered'));

        $this->_initAction()
            ->_setActiveMenu('report/product/sold')
            ->_addBreadcrumb(Mage::helper('reports')->__('Products Ordered'), Mage::helper('reports')->__('Products Ordered'))
            ->_addContent($this->getLayout()->createBlock('vendorsreport/vendor_report_product_sold'))
            ->renderLayout();
    }

    /**
     * Export Sold Products report to CSV format action
     *
     */
    public function exportSoldCsvAction()
    {
        $fileName   = 'products_ordered.csv';
        $content    = $this->getLayout()
            ->createBlock('vendorsreport/vendor_report_product_sold_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Sold Products report to XML format action
     *
     */
    public function exportSoldExcelAction()
    {
        $fileName   = 'products_ordered.xml';
        $content    = $this->getLayout()
            ->createBlock('vendorsreport/vendor_report_product_sold_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    /**
     * Render product chart (sales order, purchase order, on-hand qty)
     */
    public function chartAction(){
        $this->loadLayout();
        $output = $this->getLayout()->getOutput();
        $this->getResponse()->setBody($output);
    }
    
    protected function bestsellersAction()
    {
        $this->_title($this->__('Inventory'))
             ->_title($this->__('Reports'))
             ->_title($this->__('Product Bestsellers'));
        $this->loadLayout()
            ->_setActiveMenu('report')
            ->renderLayout();        
    }
    
    public function bestsellersgridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function detailsAction(){
        $this->loadLayout();
        $this->renderLayout();

    }
    public function detailsGridAction(){
        $this->loadLayout();
        $this->renderLayout();

    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'products.csv';
        $grid       = $this->getLayout()->createBlock('inventoryreports/adminhtml_list_product_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'products.xml';
        $grid       = $this->getLayout()->createBlock('inventoryreports/adminhtml_list_product_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
