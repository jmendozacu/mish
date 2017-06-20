<?php

class Mish_Promotionscheduler_Adminhtml_PromotionschedulerController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('promotionscheduler/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('promotionscheduler/promotionscheduler')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('promotionscheduler_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('promotionscheduler/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('promotionscheduler/adminhtml_promotionscheduler_edit'))
				->_addLeft($this->getLayout()->createBlock('promotionscheduler/adminhtml_promotionscheduler_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('promotionscheduler')->__('Item does not exist'));
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
	  			
	  			
			$model = Mage::getModel('promotionscheduler/promotionscheduler');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('promotionscheduler')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('promotionscheduler')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('promotionscheduler/promotionscheduler');
				 
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
        $promotionschedulerIds = $this->getRequest()->getParam('promotionscheduler');
        if(!is_array($promotionschedulerIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($promotionschedulerIds as $promotionschedulerId) {
                    $promotionscheduler = Mage::getModel('promotionscheduler/promotionscheduler')->load($promotionschedulerId);
                    $promotionscheduler->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($promotionschedulerIds)
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
        $promotionschedulerIds = $this->getRequest()->getParam('promotionscheduler');
        if(!is_array($promotionschedulerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($promotionschedulerIds as $promotionschedulerId) {
                    $promotionscheduler = Mage::getSingleton('promotionscheduler/promotionscheduler')
                        ->load($promotionschedulerId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($promotionschedulerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'promotionscheduler.csv';
        $content    = $this->getLayout()->createBlock('promotionscheduler/adminhtml_promotionscheduler_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'promotionscheduler.xml';
        $content    = $this->getLayout()->createBlock('promotionscheduler/adminhtml_promotionscheduler_grid')
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

    public function addnewPromoruleAction()
    {
    		$data = $this->getRequest()->getPost();
    		$couponname = $data['coupon'];
    		$code       = $data['code'];
    		$fromdate   = $data['fromdate'];
    		$todate     = $data['todate'];
    		$ruletype   = $data['ruletype'];
    		$discountamount   = $data['discountAmount'];
    		
    		$websiteId  = Mage::app()->getStore()->getWebsiteId(); 
    		
    		if($ruletype == 1){
				 $customerGroupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
				 $rule = Mage::getModel('salesrule/rule');

				 $rule->setName($couponname)                                             
				    ->setFromDate($fromdate)
				    ->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
				    ->setCouponCode($code)
				    ->setToDate($todate)
				    ->setDiscountAmount($discountamount)
				    ->setCustomerGroupIds($customerGroupIds)
				    ->setIsActive(0);

				 $productFoundCondition = Mage::getModel('salesrule/rule_condition_product_found')
				    ->setType('salesrule/rule_condition_product_found')
				    ->setValue(1)  
				    ->setAggregator('all');   
				 
				 $attributeSetCondition = Mage::getModel('salesrule/rule_condition_product')
				    ->setType('salesrule/rule_condition_product')
				    ->setAttribute('attribute_set_id')
				    ->setOperator('==')
				    ->setValue(1);

					$productFoundCondition->addCondition($attributeSetCondition);
					$rule->getConditions()->addCondition($productFoundCondition);
					$rule->getActions()->addCondition($attributeSetCondition);
					$rule->save();
				Mage::getSingleton('core/session')->addSuccess('Shopping cart rule is created, now edit the rule and make it active');
				$this->_redirect('*/*/index');
   
    		}elseif($ruletype == 2){
    			$catalogPriceRule = Mage::getModel('catalogrule/rule');
				$catalogPriceRule->setName($couponname)
					->setIsActive(0)
					->setWebsiteIds(array($websiteId))
					->setFromDate($fromdate)
					->setDiscountAmount($discountamount)
					->setToDate($todate);
				$catalogPriceRule->save();
				Mage::getSingleton('core/session')->addSuccess('Catalog rule is created, now edit the rule and make it active');
				$this->_redirect('*/*/index');
    		}elseif($ruletype == 0){
    			Mage::getSingleton('core/session')->addError('Invalid Data, Please select Coupon Type');
				$this->_redirect('*/*/index');
    		}
    }

   

    
}