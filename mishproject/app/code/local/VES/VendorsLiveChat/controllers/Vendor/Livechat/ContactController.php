<?php

class VES_VendorsLiveChat_Vendor_Livechat_ContactController extends VES_Vendors_Controller_Action
{

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('vendorslivechat/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Contact Manager'), Mage::helper('vendorslivechat')->__('Contact Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('vendorslivechat/contact')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('contact_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('vendorslivechat/contact');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Contact Manager'), Mage::helper('adminhtml')->__('Contact Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Contact View'), Mage::helper('adminhtml')->__('Contact View'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('vendorslivechat/vendor_contact_edit'))
                ->_addLeft($this->getLayout()->createBlock('vendorslivechat/vendor_contact_edit_tabs'));

            $this->renderLayout();
        } else {
            $this->_getSession()->addError(Mage::helper('vendorslivechat')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $data["vendor_id"] = Mage::getSingleton('vendors/session')->getVendor()->getId();
            $model = Mage::getModel('vendorslivechat/contact');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                $this->_getSession()->addSuccess(Mage::helper('vendorslivechat')->__('Contact was successfully saved'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_getSession()->addError(Mage::helper('vendorslivechat')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('vendorslivechat/contact');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Contact was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $vendorslivechatIds = explode(",",$this->getRequest()->getParam('contact'));
        if(!is_array($vendorslivechatIds)) {
            $this->_getSession()->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorslivechatIds as $vendorslivechatId) {
                    $vendorslivechat = Mage::getModel('vendorslivechat/contact')->load($vendorslivechatId);
                    $vendorslivechat->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($vendorslivechatIds)
                    )
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $vendorslivechatIds = $this->getRequest()->getParam('contact');
        if(!is_array($vendorslivechatIds)) {
            $this->_getSession()->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorslivechatIds as $vendorslivechatId) {
                    $vendorslivechat = Mage::getSingleton('vendorslivechat/contact')
                        ->load($vendorslivechatId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($vendorslivechatIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
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