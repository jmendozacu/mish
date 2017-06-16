<?php
class Mercadolibre_Items_Adminhtml_ShippingprofileController extends Mage_Adminhtml_Controller_action
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
		$model  = Mage::getModel('items/melishipping')->load($id);
			
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			$data = $model->getData();
			if($model->getData('shipping_method') == 'enter_shipping_cost'){
				$melishippingcustom  = Mage::getModel('items/melishippingcustom')->getCollection()-> addFieldToFilter('shipping_id',$model->getData('shipping_id'));		
				$data['shipping_custom'] = $melishippingcustom->getData();
			}
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('shippingprofiles', $model);

			$this->loadLayout();
			$this->_setActiveMenu('items/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Profiles'), Mage::helper('adminhtml')->__('Manage Profiles'));
			//$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('items/adminhtml_shippingprofile_edit'))
				->_addLeft($this->getLayout()->createBlock('items/adminhtml_shippingprofile_edit_tabs'));
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Profile does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
			try{
				if($this->getRequest()->getParam('store')){
						$storeId = (int) $this->getRequest()->getParam('store');
					} else if(Mage::helper('items')-> getMlDefaultStoreId()){
						$storeId = Mage::helper('items')-> getMlDefaultStoreId();
					} else {
						$storeId = $this->getStoreId();
					}
					
				$shipping_profile = $this->getRequest()->getPost('shipping_profile');
				$shipping_mode = $this->getRequest()->getPost('shipping_mode');
				$shipping_method = $this->getRequest()->getPost('shipping_method');
				$shipping_service_nameArr = $this->getRequest()->getPost('shipping_service_name');
				$shipping_costArr = $this->getRequest()->getPost('shipping_cost');
				$custom_shipping_idArr = $this->getRequest()->getPost('custom_shipping_id');
				$custom_shipping_idArr = $this->getRequest()->getPost('custom_shipping_id');
				
				if ($data = $this->getRequest()->getPost()) {
						// Chect Unique Shipping Profile On Edit Record
						$collectionTitle = Mage::getModel('items/melishipping')	-> getCollection()
																				-> addFieldToFilter('shipping_profile',$data['shipping_profile'])-> addFieldToSelect('shipping_id');
						$dataTitleArr = $collectionTitle->getData();	
						if(count($collectionTitle->getData()) > 0 && $dataTitleArr['0']['shipping_id'] !=  $this->getRequest()->getParam('id') ){ // New Record
							Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Shipping Profile name already exists 1 .'));
							if($this->getRequest()->getParam('id')){
								$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
							} else {
								Mage::getSingleton('adminhtml/session')->setFormData($data);
								$this->_redirect('*/*/new');
							}
							return;
						} else if(count($collectionTitle->getData()) > 0 && !$this->getRequest()->getParam('id')){ // New Record
							Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Shipping Profile name already exists 2 .'));
							if($this->getRequest()->getParam('id')){
								$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
							} else {
								Mage::getSingleton('adminhtml/session')->setFormData($data);
								$this->_redirect('*/*/new');
							}
							return;
						}
						try {
								$model  = Mage::getModel('items/melishipping');	
								$data = array(
									'shipping_profile' => $shipping_profile,
									'shipping_mode' => $shipping_mode,
									'shipping_method' => $shipping_method
									);

								$model -> setData($data) ->setId($this->getRequest()->getParam('id'));
								if (!$this->getRequest()->getParam('id')) {
									$model->setUpdatedDate(now())
										  ->setCreatedDate(now());
								} else {
									$model->setShippingId($this->getRequest()->getParam('id'));
									$model->setUpdatedDate(now());
								}
								$model->setStoreId($storeId);								
								$model->save();
								/* Save custom shipping_service_name & cost */
								$shipping_id = '';
								$shipping_id = $model->getId();
								if(count($shipping_service_nameArr) > 0 && $shipping_method == 'enter_shipping_cost'){
									foreach($shipping_service_nameArr as $key => $shipping_service_name ){
										if(trim($shipping_service_name) !=''){
											$melishippingcustom  = Mage::getModel('items/melishippingcustom');	
											$melishippingcustom -> setShippingId($shipping_id)
																-> setShippingServiceName($shipping_service_name)
																-> setShippingCost($shipping_costArr[$key]);
											if(count($custom_shipping_idArr) > 0 && isset($custom_shipping_idArr[$key]) && $custom_shipping_idArr[$key] > 0){
												$melishippingcustom -> setCustomShippingId($custom_shipping_idArr[$key]);
											}
												$melishippingcustom ->save();
												$deleteOption = '';
												if(isset($custom_shipping_idArr[$key])){
													$deleteOption = $this->getRequest()->getPost('delete_'.$custom_shipping_idArr[$key]);
													if($deleteOption == 'on'){
														$melishippingcustom ->delete();
													} 
											}
										}
									}
								}
								Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('items')->__('Shipping Profile was saved successfully '));
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
				 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__('Unable to find shipping profile to save'));
				$this->_redirect('*/*/');
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
			
				$melishippingcustom = Mage::getModel('items/melishippingcustom') -> getCollection()
																				-> addFieldToFilter('shipping_id',$this->getRequest()->getParam('id'))-> addFieldToSelect('custom_shipping_id');
				echo "<pre>";
				print_r($melishippingcustom->getData());
				exit;										      
			
				$model = Mage::getModel('items/melishipping');
				$model ->setId($this->getRequest()->getParam('id'))
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
        $shippingIds = $this->getRequest()->getParam('shippingprofile');
        if(!is_array($shippingIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Profile(s)'));
        } else {
            try {
                foreach ($shippingIds as $shippingId) {
					$melishippingcustoms = Mage::getModel('items/melishippingcustom')
										 -> getCollection()
										 -> addFieldToFilter('shipping_id',$shippingId)-> addFieldToSelect('custom_shipping_id');
					if(count($melishippingcustoms->getData()) > 0){
						foreach($melishippingcustoms->getData() as $melishippingcustom){
							 $meliShipModel = Mage::getModel('items/melishippingcustom')->load($melishippingcustom['custom_shipping_id']);
							 $meliShipModel->delete();
						}
					}										      
                    $melishipping = Mage::getModel('items/melishipping')->load($shippingId);
                    $melishipping->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($shippingIds)
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
        $itemsIds = $this->getRequest()->getParam('shippingprofile');
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
        $fileName   = 'items.csv';
        $content    = $this->getLayout()->createBlock('items/adminhtml_shippingprofile_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'items.xml';
        $content    = $this->getLayout()->createBlock('items/adminhtml_shippingprofile_grid')
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