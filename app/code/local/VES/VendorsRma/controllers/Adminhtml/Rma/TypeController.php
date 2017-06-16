<?php

class VES_VendorsRma_Adminhtml_Rma_TypeController extends Mage_Adminhtml_Controller_action
{
	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/sales/orders/type');
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
        $model  = Mage::getModel('vendorsrma/type')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('type_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('sales');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('vendorsrma')->__('Type Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('vendorsrma')->__('Type News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('vendorsrma/adminhtml_type_edit'))
                ->_addLeft($this->getLayout()->createBlock('vendorsrma/adminhtml_type_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsrma')->__('Type does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            if(in_array(0,$data['store_id'])) $data['store_id'] = 0;
            else{
                $data['store_id']=implode(',', $data['store_id']);
            }
            $model = Mage::getModel('vendorsrma/type');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorsrma')->__('Type was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsrma')->__('Unable to find type to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('vendorsrma/type');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $vendorsrmaIds = $this->getRequest()->getParam('type');
        if(!is_array($vendorsrmaIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorsrmaIds as $vendorsrmaId) {
                    $vendorsrma = Mage::getModel('vendorsrma/type')->load($vendorsrmaId);
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

    public function massStatusAction()
    {
        $vendorsrmaIds = $this->getRequest()->getParam('type');
        if(!is_array($vendorsrmaIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorsrmaIds as $vendorsrmaId) {
                    $vendorsrma = Mage::getSingleton('vendorsrma/type')
                        ->load($vendorsrmaId)
                        ->setActive($this->getRequest()->getParam('status'))
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
        $fileName   = 'vendorsrma_type.csv';
        $content    = $this->getLayout()->createBlock('vendorsrma/adminhtml_type_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'vendorsrma_type.xml';
        $content    = $this->getLayout()->createBlock('vendorsrma/adminhtml_type_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

}