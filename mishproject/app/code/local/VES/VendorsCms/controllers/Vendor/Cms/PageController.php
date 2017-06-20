<?php
class VES_VendorsCms_Vendor_Cms_PageController extends VES_Vendors_Controller_Action
{
	protected function _isAllowed()
    {
        return Mage::helper('vendorscms')->moduleEnable();
    }
	/**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_PageController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('cms/page')
            ->_addBreadcrumb(Mage::helper('vendorscms')->__('CMS'), Mage::helper('vendorscms')->__('CMS'))
        ;
        return $this;
    }
    
	public function indexAction(){
		$this->_initAction()
		->_addBreadcrumb(Mage::helper('vendorscms')->__('Manage Pages'), Mage::helper('vendorscms')->__('Manage Pages'));
		$this->renderLayout();
	}
	
	public function newAction(){
		$this->_forward('edit');
	}
	
	/**
     * Edit CMS page
     */
    public function editAction()
    {
        $this->_title($this->__('CMS'))
             ->_title($this->__('Pages'))
             ->_title($this->__('Manage Content'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('page_id');
        $model = Mage::getModel('vendorscms/page');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('vendorscms')->__('This page no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        	if($model->getId() && ($model->getVendorId() != $this->_getSession()->getVendorId())){
            	$this->_getSession()->addError(Mage::helper('vendorscms')->__('You do not have permission to access this page.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Page'));

        // 3. Set entered data if was error when we do save
        $data = $this->_getSession()->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('cms_page', $model);

        // 5. Build edit form
        $this->_initAction()
        	->_addBreadcrumb(Mage::helper('vendorscms')->__('Manage Pages'), Mage::helper('vendorscms')->__('Manage Pages'),Mage::getUrl('vendors/cms_page'))
            ->_addBreadcrumb(
                $id ? Mage::helper('vendorscms')->__('Edit Page')
                    : Mage::helper('vendorscms')->__('New Page'),
                $id ? Mage::helper('vendorscms')->__('Edit Page')
                    : Mage::helper('vendorscms')->__('New Page'));

        $this->renderLayout();
    }
    
    
	/**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            //init model and set data
            $model = Mage::getModel('vendorscms/page');

            if ($id = $this->getRequest()->getParam('page_id')) {
                $model->load($id);
	            if($model->getId() && ($model->getVendorId() != $this->_getSession()->getVendorId())){
	            	$this->_getSession()->addError(Mage::helper('vendorscms')->__('You do not have permission to access this page.'));
	                $this->_redirect('*/*/');
	                return;
	            }
            }

            $model->setData($data);

            Mage::dispatchEvent('vendorscms_page_prepare_save', array('page' => $model, 'request' => $this->getRequest()));

            // try to save it
            try {
                // save the data
                $model->setVendorId($this->_getSession()->getVendorId())->save();

                // display success message
               $this->_getSession()->addSuccess(
                    Mage::helper('vendorscms')->__('The page has been saved.'));
                // clear previously saved data from session
                $this->_getSession()->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('page_id' => $model->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('vendorscms')->__('An error occurred while saving the page.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
    
	/**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('page_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('vendorscms/page');
                $model->load($id);
                if($model->getVendorId() != $this->_getSession()->getVendorId()){
                	throw new Exception(Mage::helper('vendorscms')->__('You do not have permission to access this page.'));
                }
                $title = $model->getTitle();
                $model->delete();
                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('vendorscms')->__('The page has been deleted.'));
                // go to grid
                Mage::dispatchEvent('vendors_cmspage_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('vendors_cmspage_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                $this->_getSession()->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('page_id' => $id));
                return;
            }
        }
        // display error message
        $this->_getSession()->addError(Mage::helper('vendorscms')->__('Unable to find a page to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
	
}