<?php
class NextBits_HelpDesk_Block_Adminhtml_Ticket_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{
			$form = new Varien_Data_Form();
			$this->setForm($form);
			$fieldset = $form->addFieldset("helpdesk_form", array("legend"=>Mage::helper("helpdesk")->__("Ticket information")));
				$fieldset->addField("subject", "text", array(
				"label" => Mage::helper("helpdesk")->__("Subject"),
				"name" => "subject",
				"readonly" => true,
				));
				 $fieldset->addField('status', 'select', array(
				'label'     => Mage::helper('helpdesk')->__('Status'),
				'values'   => NextBits_HelpDesk_Helper_Data::getAllStatus(),
				'name' => 'status',
				));				
				 $fieldset->addField('priority', 'select', array(
				'label'     => Mage::helper('helpdesk')->__('Priority'),
				'values'   => NextBits_HelpDesk_Helper_Data::getAllPriority(),
				'name' => 'priority',
				));
				$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
					Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
				);
				$fieldset->addField("order", "text", array(
				"label" => Mage::helper("helpdesk")->__("Order"),
				"name" => "order",
				));
				$fieldset->addField("comment", "textarea", array(
				"label" => Mage::helper("helpdesk")->__("Comment"),
				"name" => "comment",
				));
			if (Mage::getSingleton("adminhtml/session")->getTicketData())
			{
				$form->setValues(Mage::getSingleton("adminhtml/session")->getTicketData());
				Mage::getSingleton("adminhtml/session")->setTicketData(null);
			} 
			elseif(Mage::registry("ticket_data")) {
				$form->setValues(Mage::registry("ticket_data")->getData());
			}
			return parent::_prepareForm();			
		}		
}
