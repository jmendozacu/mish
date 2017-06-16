<?php

class VES_VendorsCms_Vendor_Cms_BlockController extends VES_Vendors_Controller_Action
{
	protected function _isAllowed()
    {
        return Mage::helper('vendorscms')->moduleEnable();
    }
    /**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_BlockController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('cms/block')
            ->_addBreadcrumb(Mage::helper('vendorscms')->__('CMS'), Mage::helper('vendorscms')->__('CMS'))
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('CMS'))->_title($this->__('Static Blocks'));

        $this->_initAction()
        ->_addBreadcrumb(Mage::helper('vendorscms')->__('Static Blocks'), Mage::helper('vendorscms')->__('Static Blocks'));
        $this->renderLayout();
    }

    /**
     * Create new CMS block
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit CMS block
     */
    public function editAction()
    {
        $this->_title($this->__('CMS'))->_title($this->__('Static Blocks'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('block_id');
        $model = Mage::getModel('vendorscms/block');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $this->_getSession()->addError(Mage::helper('vendorscms')->__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        	if($model->getId() && ($model->getVendorId() != $this->_getSession()->getVendorId())){
            	$this->_getSession()->addError(Mage::helper('vendorscms')->__('You do not have permission to access this page.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Block'));

        // 3. Set entered data if was error when we do save
        $data = $this->_getSession()->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('cms_block', $model);

        // 5. Build edit form
        $this->_initAction()
        	->_addBreadcrumb(Mage::helper('vendorscms')->__('Static Blocks'), Mage::helper('vendorscms')->__('Static Blocks'),Mage::getUrl('vendors/cms_block'))
            ->_addBreadcrumb($id ? Mage::helper('vendorscms')->__('Edit Block') : Mage::helper('vendorscms')->__('New Block'), $id ? Mage::helper('vendorscms')->__('Edit Block') : Mage::helper('vendorscms')->__('New Block'))
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('block_id');
            $model = Mage::getModel('vendorscms/block')->load($id);
            if (!$model->getId() && $id) {
                $this->_getSession()->addError(Mage::helper('vendorscms')->__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
			
            if($model->getId() && ($model->getVendorId() != $this->_getSession()->getVendorId())){
            	$this->_getSession()->addError(Mage::helper('vendorscms')->__('You do not have permission to access this page.'));
                $this->_redirect('*/*/');
                return;
            }
            // init model and set data

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->setVendorId($this->_getSession()->getVendorId())->save();
                // display success message
               	$this->_getSession()->addSuccess(Mage::helper('vendorscms')->__('The block has been saved.'));
                // clear previously saved data from session
                $this->_getSession()->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('block_id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                $this->_getSession()->addError($e->getMessage());
                // save data in session
                $this->_getSession()->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('block_id' => $this->getRequest()->getParam('block_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('block_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('vendorscms/block');
                $model->load($id);
                if($model->getVendorId() != $this->_getSession()->getVendorId()){throw new Exception(Mage::helper('vendorscms')->__('You do not have permission to access this page.'));}
                $title = $model->getTitle();
                $model->delete();
                // display success message
                $this->_getSession()->addSuccess(Mage::helper('vendorscms')->__('The block has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                $this->_getSession()->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('block_id' => $id));
                return;
            }
        }
        // display error message
        $this->_getSession()->addError(Mage::helper('vendorscms')->__('Unable to find a block to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
}
