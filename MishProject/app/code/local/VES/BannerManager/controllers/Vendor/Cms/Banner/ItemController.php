<?php

class VES_BannerManager_Vendor_Cms_Banner_ItemController extends VES_Vendors_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('cms/banner')
			->_addBreadcrumb(Mage::helper('vendorscms')->__('CMS'), Mage::helper('vendorscms')->__('CMS'))
			;
		return $this;
	}   
 
	public function indexAction() {
		$this->_title($this->__('CMS'))
             ->_title($this->__('Manage Items'));
		$this->_initAction()
			->_addBreadcrumb(Mage::helper('bannermanager')->__('Manage Items'), Mage::helper('bannermanager')->__('Manage Items'))
			->renderLayout();
	}

	public function editAction() {
		$this->_title($this->__('CMS'))
             ->_title($this->__('Manage Items'));
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('bannermanager/item')->load($id);

		if ($model->getId() || $id == 0) {
			$data = $this->_getSession()->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			Mage::register('bannermanager_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('bannermanager/items');

			$this->_addBreadcrumb(Mage::helper('bannermanager')->__('Item Manager'), Mage::helper('bannermanager')->__('Item Manager'));
			if($model->getId()){
				$this->_title($this->__('Edit Item'))->_addBreadcrumb(Mage::helper('bannermanager')->__('Edit Item'), Mage::helper('bannermanager')->__('Edit Item'));
			}else{
				$this->_title($this->__('New Item'))->_addBreadcrumb(Mage::helper('bannermanager')->__('New Item'), Mage::helper('bannermanager')->__('New Item'));
			}

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('bannermanager/adminhtml_item_edit'))
				->_addLeft($this->getLayout()->createBlock('bannermanager/vendor_item_edit_tabs'));

			$this->renderLayout();
		} else {
			$this->_getSession()->addError(Mage::helper('bannermanager')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			if(isset($data['banner_id']) && $data['banner_id']){
				$banner = Mage::getModel('bannermanager/banner')->load($data['banner_id']);
				if($banner->getVendorId() != $this->_getSession()->getVendorId()){
					throw new Exception(Mage::helper('bannermanager')->__('You do not have permission to access this page.'));
				}
			}
			$vendorId = $this->_getSession()->getVendor()->getVendorId();
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS."ves_bannermanager".DS.$vendorId.DS ;
					/*
					if(!file_exists($path)){
						mkdir($path);
						chmod($path, 777);
					}
					*/
					$uploader->save($path, $_FILES['filename']['name']);
				}catch(Exception $e)
				{
				}
				$data['filename'] = "ves_bannermanager/".$vendorId.'/'.$uploader->getUploadedFileName();
			}else{
				if($data['filename']['delete']) {$data['filename'] = '';}
				else unset($data['filename']);
			}
			
			if(isset($_FILES['file_thumbnail']['name']) && $_FILES['file_thumbnail']['name'] != '') {
				try {
					$uploader = new Varien_File_Uploader('file_thumbnail');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS."ves_bannermanager".DS.$vendorId.DS ;
					if(!file_exists($path)){
						mkdir($path.DS."thumbnail".DS);
						chmod($path.DS."thumbnail", 777);
					}
					$uploader->setFilesDispersion(true);
					$uploader->save($path.DS."thumbnail".DS, $_FILES['file_thumbnail']['name'] );
				} catch (Exception $e) {
		      
		        }
		        //this way the name is saved in DB
	  			$data['file_thumbnail'] = "ves_bannermanager/".$vendorId."/thumbnail/".$uploader->getUploadedFileName();
			}else{
				if($data['file_thumbnail']['delete']) $data['file_thumbnail'] = '';
				else unset($data['file_thumbnail']);
			}
			
			$model = Mage::getModel('bannermanager/item');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			if($itemId=$this->getRequest()->getParam('id')){
				$banner_item = Mage::getModel('bannermanager/item')->load($itemId);
				Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$banner_item->getBannerId());
			}
			try {
				if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'))
						->setUpdateTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
				} else {
					$model->setUpdateTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
				}
				$model->setVendorId($this->_getSession()->getVendorId())->save();
				
				$this->_getSession()->addSuccess(Mage::helper('bannermanager')->__('Item was successfully saved'));
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
        $this->_getSession()->addError(Mage::helper('bannermanager')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('bannermanager/item')->load($this->getRequest()->getParam('id'));
				if($model->getBanner()->getVendorId() != $this->_getSession()->getVendorId()){
					throw new Exception(Mage::helper('bannermanager')->__('You do not have permission to access this page.'));
				}
				Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$model->getBannerId());
				$model->delete();
				$this->_getSession()->addSuccess(Mage::helper('bannermanager')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $bannermanagerIds = explode(",",$this->getRequest()->getParam('bannermanager'));
        if(!is_array($bannermanagerIds)) {
			$this->_getSession()->addError(Mage::helper('bannermanager')->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannermanagerIds as $bannermanagerId) {
                    $bannermanager = Mage::getModel('bannermanager/item')->load($bannermanagerId);
	                if($bannermanager->getBanner()->getVendorId() != $this->_getSession()->getVendorId()){
						throw new Exception(Mage::helper('bannermanager')->__('You do not have permission to access this page.'));
					}
                    Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$bannermanager->getBannerId());
                    $bannermanager->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('bannermanager')->__(
                        'Total of %d record(s) were successfully deleted', count($bannermanagerIds)
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
        $bannermanagerIds = explode(",",$this->getRequest()->getParam('bannermanager'));
        if(!is_array($bannermanagerIds)) {
            $this->_getSession()->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannermanagerIds as $bannermanagerId) {
                    $bannermanager = Mage::getSingleton('bannermanager/item')
                        ->load($bannermanagerId);
                	if($bannermanager->getBanner()->getVendorId() != $this->_getSession()->getVendorId()){
						throw new Exception(Mage::helper('bannermanager')->__('You do not have permission to access this page.'));
					}
                    $bannermanager->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                	Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$bannermanager->getBannerId());
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($bannermanagerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'bannermanager.csv';
        $content    = $this->getLayout()->createBlock('bannermanager/adminhtml_item_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'bannermanager.xml';
        $content    = $this->getLayout()->createBlock('bannermanager/adminhtml_item_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
	public function gridAction()
	{
		$this->getResponse()->setBody($this->getLayout()->createBlock('bannermanager/adminhtml_item_grid')->toHtml());
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