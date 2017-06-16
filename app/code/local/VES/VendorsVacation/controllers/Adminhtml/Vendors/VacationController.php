<?php

class VES_VendorsVacation_Adminhtml_Vendors_VacationController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendorsvacation/vacation')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Vacation'), Mage::helper('adminhtml')->__('Manage Vacation'));
		
		return $this;
	}

    public function indexAction() {
		$this->_initAction();
	    $this->renderLayout();
	}

	public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model = Mage::helper('vendorsvacation')->getVendorVacationData($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('vacation_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('vendorsvacation/items');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Vacation'), Mage::helper('adminhtml')->__('Manage Vacation'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Vacation'), Mage::helper('adminhtml')->__('Manage Vacation'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('vendorsvacation/adminhtml_vacation_edit'))
            ->_addLeft($this->getLayout()->createBlock('vendorsvacation/adminhtml_vacation_edit_tabs'));

        $this->renderLayout();
	}

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('vendorsvacation/adminhtml_vacation_grid')->toHtml()
        );
    }


    public function exportCsvAction()
    {
        $fileName   = 'vacation.csv';
        $content    = $this->getLayout()->createBlock('vendorsvacation/adminhtml_vacation_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'vacation.xml';
        $content    = $this->getLayout()->createBlock('vendorsvacation/adminhtml_vacation_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}