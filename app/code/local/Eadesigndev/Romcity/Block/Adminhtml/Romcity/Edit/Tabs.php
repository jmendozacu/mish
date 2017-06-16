<?php
class Eadesigndev_Romcity_Block_Adminhtml_Romcity_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("romcity_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("romcity")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("romcity")->__("Item Information"),
				"title" => Mage::helper("romcity")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("romcity/adminhtml_romcity_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
