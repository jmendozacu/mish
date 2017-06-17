<?php
class OTTO_AdvancedFaq_Block_Adminhtml_Faq_Edit_Tab_Rating extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('ticket_form_status_history', array('legend'=>Mage::helper('advancedfaq')->__('Rating')));
	
		$rating=$fieldset->addField('rating', 'text', array(
				'label'     => Mage::helper('advancedfaq')->__('Rating'),
				'name'      => 'rating',
				'class'=>'validate-number'
		));
		//$rating->setRenderer($this->getLayout()->createBlock('kbase/adminhtml_faq_edit_renderer_rating'));
		$rating=$fieldset->addField('votes', 'text', array(
				'label'     => Mage::helper('advancedfaq')->__('Votes'),
				'name'      => 'votes',
				'class'=>'validate-number'
		));
		
		if ( Mage::getSingleton('adminhtml/session')->getFaqData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getFaqData());
			Mage::getSingleton('adminhtml/session')->setFaqData(null);
		} elseif ( Mage::registry('faq_data') ) {
			$form->setValues(Mage::registry('faq_data')->getData());
		}
		return parent::_prepareForm();
	}
}