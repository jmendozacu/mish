<?php

class VES_VendorsLiveChat_Vendor_Livechat_HistoryController extends VES_Vendors_Controller_Action
{

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('vendorslivechat/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('History Manager'), Mage::helper('adminhtml')->__('History Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }

    public function viewAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('vendorslivechat/session')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('box_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('vendorslivechat/contact');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('History Manager'), Mage::helper('adminhtml')->__('History Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('View History'), Mage::helper('adminhtml')->__('View History'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('vendorslivechat/vendor_history_edit'));
                //->_addLeft($this->getLayout()->createBlock('vendorslivechat/vendor_history_edit_tabs'));

            $this->renderLayout();
        } else {
            $this->_getSession()->addError(Mage::helper('vendorslivechat')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function exportCsvAction()
    {
        $fileName   = 'contact.csv';
        $content    = $this->getLayout()->createBlock('vendorslivechat/vendor_contact_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'contact.xml';
        $content    = $this->getLayout()->createBlock('vendorslivechat/vendor_contact_grid')
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