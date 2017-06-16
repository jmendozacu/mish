<?php
class OTTO_AdvancedFaq_Block_Adminhtml_Faq_Edit_Renderer_Rating extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
	public function __construct()
	{
		$this->setTemplate('otto_advancedfaq/faq/rating.phtml');
	}
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		return $this->toHtml();
	}
	public function getVotes(){
		$faq = Mage::registry('faq_data');
		return $faq->getData('votes');
	}
	public function getRating(){
		$faq = Mage::registry('faq_data');
		return $faq->getData('rating');
	}
}