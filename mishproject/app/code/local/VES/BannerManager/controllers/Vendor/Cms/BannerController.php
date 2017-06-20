<?php

class VES_BannerManager_Vendor_Cms_BannerController extends VES_Vendors_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('cms/banner')
			->_addBreadcrumb(Mage::helper('vendorscms')->__('CMS'), Mage::helper('vendorscms')->__('CMS'))
			;
		//Mage::helper('ves_core');
		return $this;
	}   
 
	public function indexAction() {
		$this->_title($this->__('CMS'))
             ->_title($this->__('Manage Banners'));
		$this->_initAction()
			->_addBreadcrumb(Mage::helper('bannermanager')->__('Manage Banners'), Mage::helper('bannermanager')->__('Manage Banners'))
			->renderLayout();
	}

	public function editAction() {
		$this->_title($this->__('CMS'))
             ->_title($this->__('Manage Banners'));
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('bannermanager/banner')->load($id);
		
		$nivoData = unserialize($model->getNivoOptions());
        $nivoData['nivoeffect'] = explode(',', $nivoData['effect']);

        unset($nivoData['effect']);
        $model->addData($nivoData);
		
		//Mage::helper('ves_core');
		if ($model->getId() || $id == 0) {
			$data = $this->_getSession()->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			if($model->getId() && ($model->getVendorId() != $this->_getSession()->getVendorId())){
				throw new Exception(Mage::helper('bannermanager')->__('You do not have permission to access this page.'));
			}
			Mage::register('bannermanager_data', $model);
			Mage::register('current_banner', $model);
			$this->loadLayout();
			$this->_setActiveMenu('bannermanager/banner');
			$this->_addBreadcrumb(Mage::helper('bannermanager')->__('Banner Manager'), Mage::helper('bannermanager')->__('Banner Manager'));
			if($model->getId()){
				$this->_title($this->__('Edit Banner'))->_addBreadcrumb(Mage::helper('bannermanager')->__('Edit Banner'), Mage::helper('bannermanager')->__('Edit Banner'));
			}else{
				$this->_title($this->__('New Banner'))->_addBreadcrumb(Mage::helper('bannermanager')->__('New Banner'), Mage::helper('bannermanager')->__('New Banner'));
			}

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('bannermanager/vendor_banner_edit'))
				->_addLeft($this->getLayout()->createBlock('bannermanager/vendor_banner_edit_tabs'));

			$this->renderLayout();
		} else {
			$this->_getSession()->addError(Mage::helper('bannermanager')->__('Banner does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('bannermanager/banner');		
			$model->load($this->getRequest()->getParam('id'));
			
			$nivoData = array();
            $nivoData['effect'] = implode(',', $data['nivoeffect']);
			$nivoData['theme'] = $data['theme'];
			$nivoData['slices'] = $data['slices'];
			$nivoData['boxCols'] = $data['boxCols'];
			$nivoData['boxRows'] = $data['boxRows'];
			$nivoData['animSpeed'] = $data['animSpeed'];
			$nivoData['pauseTime'] = $data['pauseTime'];
			$nivoData['directionNav'] = $data['directionNav'];
			$nivoData['controlNav'] = $data['controlNav'];
			$nivoData['pauseOnHover'] = $data['pauseOnHover'];
			$nivoData['manualAdvance'] = $data['manualAdvance'];
			$serializeNivo = serialize($nivoData);
			$data['nivo_options'] = $serializeNivo;
				
			//Mage::helper('ves_core');
			try {
				if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'))
						->setUpdateTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
				} else {
					$model->setUpdateTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
				}	
				$vendorId = $this->_getSession()->getVendorId();
				if($model->getId() && ($model->getVendorId() != $vendorId)){
					throw new Exception(Mage::helper('bannermanager')->__('You do not have permission to access this page.'));
				}
				$model->setData($data)->setId($this->getRequest()->getParam('id'))->setVendorId($vendorId)->save();
				Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$model->getId());
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
		//Mage::helper('ves_core');
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('bannermanager/banner');
				$model->load($this->getRequest()->getParam('id'));
				if($model->getId() && ($model->getVendorId() != $this->_getSession()->getVendorId())){
					throw new Exception(Mage::helper('bannermanager')->__('You do not have permission to access this page.'));
				}
				Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$model->getId());
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
        //Mage::helper('ves_core');
        if(!is_array($bannermanagerIds)) {
			$this->_getSession()->addError(Mage::helper('bannermanager')->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannermanagerIds as $bannermanagerId) {
                    $bannermanager = Mage::getModel('bannermanager/banner')->load($bannermanagerId);
	                if($bannermanager->getId() && ($bannermanager->getVendorId() != $this->_getSession()->getVendorId())){
						throw new Exception(Mage::helper('bannermanager')->__('You do not have permission to access this page.'));
					}
                    Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$bannermanagerId);
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
        //Mage::helper('ves_core');
        if(!is_array($bannermanagerIds)) {
            $this->_getSession()->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannermanagerIds as $bannermanagerId) {
                    $bannermanager = Mage::getSingleton('bannermanager/banner')
                        ->load($bannermanagerId);
                	if($bannermanager->getId() && ($bannermanager->getVendorId() != $this->_getSession()->getVendorId())){
						throw new Exception(Mage::helper('bannermanager')->__('You do not have permission to access this page.'));
					}
                    $bannermanager->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                    Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$bannermanagerId);
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
        $content    = $this->getLayout()->createBlock('bannermanager/adminhtml_banner_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'bannermanager.xml';
        $content    = $this->getLayout()->createBlock('bannermanager/adminhtml_banner_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
	public function gridAction()
	{
		$this->getResponse()->setBody($this->getLayout()->createBlock('bannermanager/adminhtml_banner_grid')->toHtml());
	}
	public function itemgridAction()
	{
		$banner_id = $this->getRequest()->getParam('banner_id');
		Mage::register('current_banner',Mage::getModel('bannermanager/banner')->load($banner_id));
		$this->getResponse()->setBody($this->getLayout()->createBlock('bannermanager/adminhtml_banner_edit_tab_itemgrid')->toHtml());
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