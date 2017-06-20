<?php

class VES_VendorsRma_Adminhtml_Rma_MestemplateController extends Mage_Adminhtml_Controller_action
{

	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/sales/orders/mestemplate');
    }
	
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('sales')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('vendorsrma/mestemplate')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('mestemplate_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('sales');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Message Template Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Message Template Manager'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('vendorsrma/adminhtml_mestemplate_edit'))
                ->_addLeft($this->getLayout()->createBlock('vendorsrma/adminhtml_mestemplate_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsrma')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('vendorsrma/mestemplate');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorsrma')->__('Template was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsrma')->__('Unable to find template to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('vendorsrma/mestemplate');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Template was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $vendorsrmaIds = $this->getRequest()->getParam('mestemplate');
        if(!is_array($vendorsrmaIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorsrmaIds as $vendorsrmaId) {
                    $vendorsrma = Mage::getModel('vendorsrma/mestemplate')->load($vendorsrmaId);
                    $vendorsrma->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($vendorsrmaIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Preview action
     */
    public function previewAction()
    {
        $id = $this->getRequest()->getParam("template");
        $template = Mage::getSingleton('vendorsrma/mestemplate')->load($id);
        if(!$id || !$template->getId()){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsrma')->__('Unable to find template to preview'));
            $this->_redirect('*/*/index');
            return;
        }
        
        $this->loadLayout('systemPreviewTemplateRma');
        $this->renderLayout();
    }
    
    
    public function massStatusAction()
    {
        $vendorsrmaIds = $this->getRequest()->getParam('mestemplate');
        if(!is_array($vendorsrmaIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorsrmaIds as $vendorsrmaId) {
                    $vendorsrma = Mage::getSingleton('vendorsrma/mestemplate')
                        ->load($vendorsrmaId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($vendorsrmaIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'vendorsrma_reason.csv';
        $content    = $this->getLayout()->createBlock('vendorsrma/adminhtml_mestemplate_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'vendorsrma_reason.xml';
        $content    = $this->getLayout()->createBlock('vendorsrma/adminhtml_mestemplate_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
}