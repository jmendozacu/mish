<?php
class VES_VendorsReview_Adminhtml_Vendors_RatingController extends Mage_Adminhtml_Controller_Action
{
protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendorsreview/rating')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manager Ratings'), Mage::helper('adminhtml')->__('Manager Ratings'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vendorsreview/rating')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('rating_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('vendorsreview/rating');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manager Ratings'), Mage::helper('adminhtml')->__('Manager Ratings'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Rating News'), Mage::helper('adminhtml')->__('Rating News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('vendorsreview/adminhtml_rating_edit'))
				->_addLeft($this->getLayout()->createBlock('vendorsreview/adminhtml_rating_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsreview')->__('Rating does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {		
			$data['rating_code'] = strtolower($data['title']);
			$model = Mage::getModel('vendorsreview/rating');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				$model->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorsreview')->__('Rating was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsreview')->__('Unable to find rating to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('vendorsreview/rating');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Rating was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $productquestionIds = $this->getRequest()->getParam('vendorsreview');
        if(!is_array($productquestionIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select ratings'));
        } else {
            try {
                foreach ($productquestionIds as $productquestionId) {
                    $productquestion = Mage::getModel('vendorsreview/rating')->load($productquestionId);
                    $productquestion->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($productquestionIds)
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

    }
}