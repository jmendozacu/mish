<?php
class VES_VendorsReview_Adminhtml_Vendors_ReviewController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()
		->_setActiveMenu('vendorsreview/review')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Manager Reviews'), Mage::helper('adminhtml')->__('Manager Reviews'));
	
		return $this;
	}
	
	public function indexAction() {
		//set admin mode
		Mage::register('useAdminMode', true);
		$this->_initAction()
		->renderLayout();
	}
	
	public function editAction() {
		Mage::register('useAdminMode', true);
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vendorsreview/review')->load($id);
	
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			Mage::register('review_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('vendorsreview/review');
	
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manager Reviews'), Mage::helper('adminhtml')->__('Manager Reviews'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Review News'), Mage::helper('adminhtml')->__('Review News'));
	
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
	
			$this->_addContent($this->getLayout()->createBlock('vendorsreview/adminhtml_review_edit'))
			->_addLeft($this->getLayout()->createBlock('vendorsreview/adminhtml_review_edit_tabs'));
	
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsreview')->__('Review does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function newAction() {
		//$this->_forward('edit');
		$this->_redirect('*/*/');
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
				
			$model = Mage::getModel('vendorsreview/review');
			$model->setData($data)
			->setId($this->getRequest()->getParam('id'));
				
			try {
				$model->save();
	
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorsreview')->__('Review was successfully saved'));
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
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsreview')->__('Unable to find review to save'));
		$this->_redirect('*/*/');
	}
	
	public function deleteAction() {
// 		if( $this->getRequest()->getParam('id') > 0 ) {
// 			try {
// 				$model = Mage::getModel('vendorsreview/review');
					
// 				$model->setId($this->getRequest()->getParam('id'))
// 				->delete();
	
// 				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Review was successfully deleted'));
// 				$this->_redirect('*/*/');
// 			} catch (Exception $e) {
// 				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
// 				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
// 			}
// 		}
		$this->_redirect('*/*/');
	}
	
	public function massDeleteAction() {
// 		$productquestionIds = $this->getRequest()->getParam('vendorsreview');
// 		if(!is_array($productquestionIds)) {
// 			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select reviews'));
// 		} else {
// 			try {
// 				foreach ($productquestionIds as $productquestionId) {
// 					$productquestion = Mage::getModel('vendorsreview/review')->load($productquestionId);
// 					$productquestion->delete();
// 				}
// 				Mage::getSingleton('adminhtml/session')->addSuccess(
// 				Mage::helper('adminhtml')->__(
// 				'Total of %d record(s) were successfully deleted', count($productquestionIds)
// 				)
// 				);
// 			} catch (Exception $e) {
// 				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
// 			}
// 		}
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction()
	{
		$reviewIds = $this->getRequest()->getParam('vendorsreview');
		if(!is_array($reviewIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select reviews'));
		} else {
			try {
				foreach ($reviewIds as $reviewId) {
					$review = Mage::getSingleton('vendorsreview/review')
					->load($reviewId)
					->setStatus($this->getRequest()->getParam('status'))
					->setIsMassupdate(true);
					
					//set ratings
					$ratings = $review->getVoteToMassaction();
					$review->setRatings($ratings);
					$review->save();
				}
				$this->_getSession()->addSuccess(
						$this->__('Total of %d record(s) were successfully updated', count($reviewIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
}