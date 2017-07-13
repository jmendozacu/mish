<?php

class VES_VendorsImportProduct_Vendors_QuesanswerController extends VES_Vendors_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('quesanswer/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {

		echo "string";
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('quesanswer/quesanswer')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('vendors/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('quesanswer_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('quesanswer/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('quesanswer/vendors_quesanswer_edit'))
				->_addLeft($this->getLayout()->createBlock('quesanswer/vendors_quesanswer_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('vendors/session')->addError(Mage::helper('quesanswer')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('quesanswer/quesanswer');		
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
				Mage::getSingleton('vendors/session')->addSuccess(Mage::helper('quesanswer')->__('Item was successfully saved'));
				Mage::getSingleton('vendors/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
                Mage::getSingleton('vendors/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('vendors/session')->addError(Mage::helper('quesanswer')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('quesanswer/quesanswer');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('vendors/session')->addSuccvendorsess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('vendors/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $quesanswerIds = $this->getRequest()->getParam('quesanswer');
        if(!is_array($quesanswerIds)) {
			Mage::getSingleton('vendors/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($quesanswerIds as $quesanswerId) {
                    $quesanswer = Mage::getModel('quesanswer/quesanswer')->load($quesanswerId);
                    $quesanswer->delete();
                }
                Mage::getSingleton('vendors/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($quesanswerIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('vendors/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $quesanswerIds = $this->getRequest()->getParam('quesanswer');
        if(!is_array($quesanswerIds)) {
            Mage::getSingleton('vendors/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($quesanswerIds as $quesanswerId) {
                    $quesanswer = Mage::getSingleton('quesanswer/quesanswer')
                        ->load($quesanswerId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($quesanswerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'quesanswer.csv';
        $content    = $this->getLayout()->createBlock('quesanswer/vendors_quesanswer_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'quesanswer.xml';
        $content    = $this->getLayout()->createBlock('quesanswer/vendors_quesanswer_grid')
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