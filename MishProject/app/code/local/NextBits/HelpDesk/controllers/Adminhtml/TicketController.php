<?php

class NextBits_HelpDesk_Adminhtml_TicketController extends Mage_Adminhtml_Controller_Action
{		
	const XML_PATH_HELPDESK_EMAIL_TEMPLATE  = 'help_desk/email/email_template';
	const XML_PATH_HELPDESK_SENDER_EMAIL    = 'help_desk/email/help_desk_email';
	const XML_PATH_HELPDESK_SENDER_NAME     = 'help_desk/email/help_desk_name';
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("helpdesk/ticket")->_addBreadcrumb(Mage::helper("adminhtml")->__("Ticket  Manager"),Mage::helper("adminhtml")->__("Ticket Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("HelpDesk"));
			    $this->_title($this->__("Manager Ticket"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("HelpDesk"));
				$this->_title($this->__("Ticket"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("helpdesk/ticket")->load($id);
				if ($model->getId()) {
					Mage::register("ticket_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("helpdesk/ticket");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Ticket Manager"), Mage::helper("adminhtml")->__("Ticket Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Ticket Description"), Mage::helper("adminhtml")->__("Ticket Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("helpdesk/adminhtml_ticket_edit"))->_addLeft($this->getLayout()->createBlock("helpdesk/adminhtml_ticket_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("helpdesk")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}
		
		public function newAction()
		{
		$this->_title($this->__("HelpDesk"));
		$this->_title($this->__("Ticket"));
		$this->_title($this->__("New Item"));
        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("helpdesk/ticket")->load($id);
		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
		Mage::register("ticket_data", $model);
		$this->loadLayout();
		$this->_setActiveMenu("helpdesk/ticket");
		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Ticket Manager"), Mage::helper("adminhtml")->__("Ticket Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Ticket Description"), Mage::helper("adminhtml")->__("Ticket Description"));
		$this->_addContent($this->getLayout()->createBlock("helpdesk/adminhtml_ticket_edit"))->_addLeft($this->getLayout()->createBlock("helpdesk/adminhtml_ticket_edit_tabs"));
		$this->renderLayout();
		}
		public function saveAction()
		{
			$post_data = $this->getRequest()->getPost();
			$id = $this->getRequest()->getParam('ticket_id');
				if ($post_data) {
					try {
						$ticket = Mage::getModel("helpdesk/ticket")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();
						$user = Mage::getSingleton('admin/session');
						$name = $user->getUser()->getData('firstname')." ".$user->getUser()->getData('lastname');
						$comment = Mage::getModel("helpdesk/comment")
						->setData('comment',$post_data['comment'])
						->setData('comment_by',$name)
						->setData('created_date', now())
						->setData('is_customer_comment', 0)						
						->setData('ticket_id',$ticket->getId())						
						->save();
						$createDate = $comment->getData('created_date');
						$ticket  = Mage::getModel('helpdesk/ticket')->load($ticket->getData('ticket_id'));
						$ticket->setData('updated_date', $createDate);
						$ticket->save();
						Mage::helper('helpdesk')->sendMail($ticket->getId());
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Ticket was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setTicketData(false);
						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $ticket->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setTicketData($this->getRequest()->getPost());
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
					$model = Mage::getModel("helpdesk/ticket");
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
				$ids = $this->getRequest()->getPost('ticket_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("helpdesk/ticket");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Ticket(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
		public function exportCsvAction()
		{
			$fileName   = 'ticket.csv';
			$grid       = $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		public function exportExcelAction()
		{
			$fileName   = 'ticket.xml';
			$grid       = $this->getLayout()->createBlock('helpdesk/adminhtml_ticket_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
