<?php

class VES_BannerManager_Adminhtml_BannerController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('bannermanager/banner')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Banner Manager'), Mage::helper('adminhtml')->__('Banner Manager'));
		//Mage::helper('ves_core');
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('bannermanager/banner')->load($id);
		//Mage::helper('ves_core');
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('bannermanager_data', $model);
			Mage::register('current_banner', $model);
			$this->loadLayout();
			$this->_setActiveMenu('bannermanager/banner');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Banner Manager'), Mage::helper('adminhtml')->__('Banner Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Banner News'), Mage::helper('adminhtml')->__('Banner News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('bannermanager/adminhtml_banner_edit'))
				->_addLeft($this->getLayout()->createBlock('bannermanager/adminhtml_banner_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannermanager')->__('Banner does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('bannermanager/banner');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			//Mage::helper('ves_core');
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'))
						->setUpdateTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
				} else {
					$model->setUpdateTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
				}	
				
				$model->save();
				Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$model->getId());
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('bannermanager')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannermanager')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		//Mage::helper('ves_core');
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('bannermanager/bannermanager');
				$model->setId($this->getRequest()->getParam('id'));
				Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$model->getId());
				$model->delete();
					 
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
        $bannermanagerIds = $this->getRequest()->getParam('bannermanager');
        //Mage::helper('ves_core');
        if(!is_array($bannermanagerIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannermanagerIds as $bannermanagerId) {
                    $bannermanager = Mage::getModel('bannermanager/banner')->load($bannermanagerId);
                    Mage::app()->cleanCache(VES_BannerManager_Model_Banner::CACHE_TAG."_".$bannermanagerId);
                    $bannermanager->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($bannermanagerIds)
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
        $bannermanagerIds = $this->getRequest()->getParam('bannermanager');
        //Mage::helper('ves_core');
        if(!is_array($bannermanagerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannermanagerIds as $bannermanagerId) {
                    $bannermanager = Mage::getSingleton('bannermanager/banner')
                        ->load($bannermanagerId)
                        ->setStatus($this->getRequest()->getParam('status'))
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