<?php
class NextBits_HelpDesk_IndexController extends Mage_Core_Controller_Front_Action{
	
	const XML_PATH_ENABLED   = 'help_desk/email/enabled';
	protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	public function preDispatch()
    {
        parent::preDispatch();
		if( !Mage::getStoreConfigFlag(self::XML_PATH_ENABLED) ) {
            $this->norouteAction();
        }
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        $action = $this->getRequest()->getActionName();
        $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }
    public function indexAction() 
	{
		$this->loadLayout();   
		$this->getLayout()->getBlock("head")->setTitle($this->__("My Support Tickets"));
		$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
				"label" => $this->__("Home Page"),
				"title" => $this->__("Home Page"),
				"link"  => Mage::getBaseUrl()
		   ));
		$breadcrumbs->addCrumb("my support tickets", array(
				"label" => $this->__("My Support Tickets"),
				"title" => $this->__("My Support Tickets")
		   ));
		$this->renderLayout(); 
    }
	public function saveAction()
    {
		$customerName = Mage::getSingleton('customer/session')->getCustomer()->getName();
		$ticketPost = $this->getRequest()->getPost();
        $ticket  = Mage::getModel('helpdesk/ticket');
		$ticket->setData($ticketPost);
		$ticket->setData('status', 'New');		
		$ticket->setData('created_date', now());		
		$ticket->setData('updated_date', now());		
		$ticket->save();
		Mage::helper('helpdesk')->sendMail($ticket->getTicketId());
		Mage::getSingleton("core/session")->addSuccess(Mage::helper("helpdesk")->__("Ticket has been successfully saved."));
        $this->_redirect('helpdesk/index/index'); 
    }
	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		if(!empty($id)){
			$ticket = Mage::getModel('helpdesk/ticket')->load($id);
			$ticketCustomerId = $ticket->getData('customer_id');
			$loggedInCustomerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
			if($ticketCustomerId==$loggedInCustomerId){
				Mage::register('current_ticket', $ticket);
				$this->loadLayout();   
				$this->renderLayout(); 
				return;
			}else{
				Mage::getSingleton("core/session")->addError(Mage::helper("helpdesk")->__("No such ticket exists with your account."));
			}
		}else{
			Mage::getSingleton("core/session")->addError(Mage::helper("helpdesk")->__("No such ticket exists."));
		}			
		$this->_redirect('helpdesk/index/index');   
	}
	public function commentAction(){
		$id = $this->getRequest()->getParam('ticket_id');
		$commentPost = $this->getRequest()->getPost();
		$customerName = Mage::getSingleton('customer/session')->getCustomer()->getName();
		$comment  = Mage::getModel('helpdesk/comment');
		$comment->setData($commentPost);
		$comment->setData('created_date', now());
		$comment->setData('comment_by',$customerName);
		$comment->setData('is_customer_comment', 1);
		$comment->save();
		$createDate = $comment->getData('created_date');
		$ticket  = Mage::getModel('helpdesk/ticket')->load($id);
		$ticket->setData('updated_date', $createDate);
		$ticket->save();
		Mage::helper('helpdesk')->sendMail($id);	
		Mage::getSingleton("core/session")->addSuccess(Mage::helper("helpdesk")->__("Reply has been successfully saved."));		
		$this->_redirect('helpdesk/index/view/id/'.$id);
	}
	public function closeTicketAction(){
		$id = $this->getRequest()->getParam('id');
		if(!empty($id)){
			$ticket = Mage::getModel('helpdesk/ticket')->load($id);
			$ticketCustomerId = $ticket->getData('customer_id');
			$loggedInCustomerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
			if($ticketCustomerId==$loggedInCustomerId){
				$ticket->setData('status' , 'Closed');
				$ticket->save();
				Mage::getSingleton("core/session")->addSuccess(Mage::helper("helpdesk")->__("Ticket has been successfully closed."));
			}else{
				Mage::getSingleton("core/session")->addError(Mage::helper("helpdesk")->__("No such ticket exists with your account."));
			}
		}else{
			Mage::getSingleton("core/session")->addError(Mage::helper("helpdesk")->__("No such ticket exists."));
		}			
		$this->_redirect('helpdesk/index/view/id/'.$id);   
	}
}