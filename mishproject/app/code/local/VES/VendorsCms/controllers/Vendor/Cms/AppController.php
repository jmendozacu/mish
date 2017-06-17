<?php
class VES_VendorsCms_Vendor_Cms_AppController extends VES_Vendors_Controller_Action
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
            ->_setActiveMenu('cms/app')
            ->_addBreadcrumb(Mage::helper('vendorscms')->__('CMS'), Mage::helper('vendorscms')->__('CMS'))
        ;
        return $this;
    }
    
	public function indexAction(){
		$this->_title($this->__('CMS'))
             ->_title($this->__('Manage Frontend Apps Instances'));
		$this->_initAction()
		->_addBreadcrumb(Mage::helper('vendorscms')->__('Manage Frontend Apps Instances'), Mage::helper('vendorscms')->__('Manage Frontend Apps Instances'));
		$this->renderLayout();
	}
	
	public function newAction(){
		$this->_forward('edit');
	}
	public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];
		if($type=='catalogrule/rule_condition_product') $type='vendorscms/rule_condition_product';
		if($type=='catalogrule/rule_condition_combine') $type='vendorscms/rule_condition_combine';
        $model = Mage::getModel($type)
        	->setNumber($this->getRequest()->getParam('number'))
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('vendorscms/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody(str_replace("{{number}}", $this->getRequest()->getParam('number'), $html));
	}
    /**
     * Process Conditions
     */
	public function processConditionsAction(){
		$number = $this->getRequest()->getParam('number');
		$conditions = $this->getRequest()->getParam('conditions');
		$model = Mage::getModel('vendorscms/rule');
		$model->setConditionsSerialized($conditions);
		$model->getConditions()->setJsFormObject('rule_conditions_fieldset_{{number}}');
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('ves_vendorscms/promo/fieldset.phtml')
            ->setNewChildUrl(Mage::helper('adminhtml')->getUrl('*/*/newConditionHtml/form/rule_conditions_fieldset_{{number}}/number/{{number}}'));

        $fieldset = $form->addFieldset('conditions_fieldset_{{number}}', array(
            'legend'=>Mage::helper('catalogrule')->__('Conditions (leave blank for all products)'))
        )->setRenderer($renderer);

        $fieldset->addField('conditions_{{number}}', 'text', array(
            'name' => 'conditions[{{number}}]',
            'label' => Mage::helper('catalogrule')->__('Conditions'),
            'title' => Mage::helper('catalogrule')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
        $this->getResponse()->setBody(str_replace("{{number}}", $number, $form->toHtml()));
	}
	/**
     * Edit CMS page
     */
    public function editAction()
    {
        $this->_title($this->__('CMS'))
             ->_title($this->__('Manage Frontend Apps Instances'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('app_id');
        $model = Mage::getModel('vendorscms/app');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('vendorscms')->__('This App no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        	if($model->getId() && ($model->getVendorId() != $this->_getSession()->getVendorId())){
            	$this->_getSession()->addError(Mage::helper('vendorscms')->__('You do not have permission to access this page.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New App'));

        // 3. Set entered data if was error when we do save
        $data = $this->_getSession()->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('cms_app', $model);

        // 5. Build edit form
        $this->_initAction()
        	->_addBreadcrumb(Mage::helper('vendorscms')->__('Manage Frontend Apps Instances'), Mage::helper('vendorscms')->__('Manage Frontend Apps Instances'),Mage::getUrl('vendors/cms_app'))
            ->_addBreadcrumb(
                $id ? Mage::helper('vendorscms')->__('Edit App')
                    : Mage::helper('vendorscms')->__('New App'),
                $id ? Mage::helper('vendorscms')->__('Edit App')
                    : Mage::helper('vendorscms')->__('New App'));

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
            $model = Mage::getModel('vendorscms/app');

            if ($id = $this->getRequest()->getParam('app_id')) {
                $model->load($id);
	            if($model->getId() && ($model->getVendorId() != $this->_getSession()->getVendorId())){
	            	$this->_getSession()->addError(Mage::helper('vendorscms')->__('You do not have permission to access this page.'));
	                $this->_redirect('*/*/');
	                return;
	            }
            }
            $model->setData($data);
			if(($type = $this->getRequest()->getParam('type')) && !$model->getId()){
				$model->setType($type);
			}
            Mage::dispatchEvent('vendorscms_app_prepare_save', array('page' => $model, 'request' => $this->getRequest()));

            // try to save it
            try {
                // save the data
                $model->setVendorId($this->_getSession()->getVendorId())->save();
				Mage::dispatchEvent('vendorscms_app_save_after', array('app' => $model, 'request' => $this->getRequest()));
				
				/*Save frontend instances*/
				$optionIds = array();
				if(isset($data['frontend_instance'])){
					foreach($data['frontend_instance'] as $instanceData){
						$option = Mage::getModel('vendorscms/appoption');
						$data = array(
							'app_id'	=> $model->getId(),
							'code'		=> 'frontend_instance',
						);
						$option->setData($data);
						if($instanceData['option_id']){
							$option->setId($instanceData['option_id']);
						}
						unset($instanceData['option_id']);
						
						$option->setData('value',json_encode($instanceData));
						$option->save();
						$optionIds[] = $option->getId();
					}
				}
				/*Delete all other related frontend instance options which is not in $optionIds array*/
				Mage::getResourceModel('vendorscms/appoption')->checkAndDeleteFrontendInstanceOptions($model->getId(), $optionIds);
				
                // display success message
               $this->_getSession()->addSuccess(
                    Mage::helper('vendorscms')->__('The frontend instance has been saved.'));
                // clear previously saved data from session
                $this->_getSession()->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('app_id' => $model->getId(), '_current'=>true));
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
        if ($id = $this->getRequest()->getParam('app_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('vendorscms/app');
                $model->load($id);
                if($model->getVendorId() != $this->_getSession()->getVendorId()){
                	throw new Exception(Mage::helper('vendorscms')->__('You do not have permission to access this page.'));
                }
                $title = $model->getTitle();
                $model->delete();
                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('vendorscms')->__('The app has been deleted.'));
                // go to grid
                Mage::dispatchEvent('vendors_cmsapp_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('vendors_cmsapp_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                $this->_getSession()->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('page_id' => $id));
                return;
            }
        }
        // display error message
        $this->_getSession()->addError(Mage::helper('vendorscms')->__('Unable to find an app to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
	
}