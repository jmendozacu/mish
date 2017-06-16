<?php
	
class NextBits_HelpDesk_Block_Adminhtml_Ticket_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "ticket_id";
				$this->_blockGroup = "helpdesk";
				$this->_controller = "adminhtml_ticket";
				$this->_updateButton("save", "label", Mage::helper("helpdesk")->__("Save Ticket"));
				$this->_updateButton("delete", "label", Mage::helper("helpdesk")->__("Delete Ticket"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("helpdesk")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{		
				if( Mage::registry("ticket_data") && Mage::registry("ticket_data")->getId() ){
					$subject = Mage::registry("ticket_data")->getSubject();
				    return Mage::helper("helpdesk")->__("Ticket  '%s'", $this->htmlEscape($subject));

				} 
				else{

				     return Mage::helper("helpdesk")->__("Add Ticket");

				}
		}
		 
}