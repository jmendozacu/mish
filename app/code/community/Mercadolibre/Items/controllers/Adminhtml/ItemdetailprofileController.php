<?php

class Mercadolibre_Items_Adminhtml_ItemdetailprofileController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('items/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Profiles'), Mage::helper('adminhtml')->__('Manage Profiles'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
		
	}

	public function editAction() {

		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('items/meliitemprofiledetail')->load($id);
		
		if ($model->getId() || $id == 0) {
			
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('itemdetailprofile', $model);

			$this->loadLayout();
			$this->_setActiveMenu('items/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Profiles'), Mage::helper('adminhtml')->__('Manage Profiles'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('items/adminhtml_itemdetailprofile_edit'))
				->_addLeft($this->getLayout()->createBlock('items/adminhtml_itemdetailprofile_edit_tabs'));
			$this->renderLayout();
		
		}else {
			
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Profile does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
			try{
				if ($data = $this->getRequest()->getPost()) {
					if($this->getRequest()->getParam('store')){
						$storeId = (int) $this->getRequest()->getParam('store');
					} else if(Mage::helper('items')-> getMlDefaultStoreId()){
						$storeId = Mage::helper('items')-> getMlDefaultStoreId();
					} else {
						$storeId = $this->getStoreId();
					}
					if (!$this->getRequest()->getParam('id')) {

						//Check Unique Template Name On Edit Record
						 $collectionTitle = Mage::getModel('items/meliitemprofiledetail') -> getCollection()
																						 -> addFieldToFilter('profile_name',trim($data['profile_name']))-> addFieldToSelect('profile_id');
						}else{
							//Check Unique Template Name On Edit Record
							$collectionTitle = Mage::getModel('items/meliitemprofiledetail') -> getCollection()
																						 -> addFieldToFilter('profile_id',$this->getRequest()->getParam('id'))-> addFieldToSelect('profile_id');
						
						
						}
						
						$dataTitleArr = $collectionTitle->getData();	
						
						if(count($dataTitleArr)>0){
							$dataToSave['profile_id']= $dataTitleArr['0']['profile_id'];
						}
						//$dataToSave['profile_id']= $data['profile_id'];
						if(isset($data['item_id'])){ $item_id = $data['item_id']; } else { $item_id = '';}
						if(isset($data['product_name'])) { $product_name = $data['product_name']; } else { $product_name = ''; }
						$dataToSave['profile_name']= $data['profile_name'];
						$dataToSave['item_id'] = $item_id;
						$dataToSave['product_name']= $product_name;
						$dataToSave['description_header']= $data['description_header'];
						$dataToSave['description_body']= $data['description_body'];
						$dataToSave['description_footer']= $data['description_footer'];
						$dataToSave['store_id']= $storeId;
						

						$model  = Mage::getModel('items/meliitemprofiledetail');		
						$model	-> setData($dataToSave);// ->setId($this->getRequest()->getParam('id'));
						if (!$this->getRequest()->getParam('id')) {
							$model->setDateCreated(now())
								 ->setDateUpdated(now());
						} else {
							$model->setDateUpdated(now());
						}	
						
						$model->save();

						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Profile was saved successfully '));
							Mage::getSingleton('adminhtml/session')->setFormData(false);
			
							if ($this->getRequest()->getParam('back')) {
								$this->_redirect('*/*/edit', array('id' => $model->getId()));
								return;
							}
							$this->_redirect('*/*/');
							return;
					}	
				} catch (Exception $e) {
							Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
							Mage::getSingleton('adminhtml/session')->setFormData($data);
							$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
							return;
				}
				
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('items/meliitemprofiledetail');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Profile was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $itemsIds = $this->getRequest()->getParam('itemdetailprofile');
        if(!is_array($itemsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Profile(s)'));
        } else {
            try {
                foreach ($itemsIds as $itemsId) {
                    $items = Mage::getModel('items/meliitemprofiledetail')->load($itemsId);
                    $items->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($itemsIds)
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
        $itemsIds = $this->getRequest()->getParam('items');
        if(!is_array($itemsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Profile(s)'));
        } else {
            try {
                foreach ($itemsIds as $itemsId) {
                    $items = Mage::getSingleton('items/items')
                        ->load($itemsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($itemsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'itemdetailprofile.csv';
        $content    = $this->getLayout()->createBlock('items/adminhtml_itemdetailprofile_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'itemdetailprofile.xml';
        $content    = $this->getLayout()->createBlock('items/adminhtml_itemdetailprofile_grid')
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