<?php

class Eadesigndev_Romcity_Adminhtml_RomcityController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('romcity/romcity');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("romcity/romcity")->_addBreadcrumb(Mage::helper("adminhtml")->__("Romcity  Manager"),Mage::helper("adminhtml")->__("Romcity Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Romcity"));
			    $this->_title($this->__("Manager Romcity"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Romcity"));
				$this->_title($this->__("Romcity"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("romcity/romcity")->load($id);
				if ($model->getId()) {
					Mage::register("romcity_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("romcity/romcity");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Romcity Manager"), Mage::helper("adminhtml")->__("Romcity Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Romcity Description"), Mage::helper("adminhtml")->__("Romcity Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("romcity/adminhtml_romcity_edit"))->_addLeft($this->getLayout()->createBlock("romcity/adminhtml_romcity_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("romcity")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Romcity"));
		$this->_title($this->__("Romcity"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("romcity/romcity")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("romcity_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("romcity/romcity");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Romcity Manager"), Mage::helper("adminhtml")->__("Romcity Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Romcity Description"), Mage::helper("adminhtml")->__("Romcity Description"));


		$this->_addContent($this->getLayout()->createBlock("romcity/adminhtml_romcity_edit"))->_addLeft($this->getLayout()->createBlock("romcity/adminhtml_romcity_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("romcity/romcity")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Romcity was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setRomcityData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setRomcityData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("romcity/romcity");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('city_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("romcity/romcity");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'romcity.csv';
			$grid       = $this->getLayout()->createBlock('romcity/adminhtml_romcity_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'romcity.xml';
			$grid       = $this->getLayout()->createBlock('romcity/adminhtml_romcity_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
