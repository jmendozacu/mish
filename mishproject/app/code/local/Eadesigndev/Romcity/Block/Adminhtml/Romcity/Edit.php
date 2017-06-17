<?php
	
class Eadesigndev_Romcity_Block_Adminhtml_Romcity_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "city_id";
				$this->_blockGroup = "romcity";
				$this->_controller = "adminhtml_romcity";
				$this->_updateButton("save", "label", Mage::helper("romcity")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("romcity")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("romcity")->__("Save And Continue Edit"),
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
				if( Mage::registry("romcity_data") && Mage::registry("romcity_data")->getId() ){

				    return Mage::helper("romcity")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("romcity_data")->getId()));

				} 
				else{

				     return Mage::helper("romcity")->__("Add Item");

				}
		}
}