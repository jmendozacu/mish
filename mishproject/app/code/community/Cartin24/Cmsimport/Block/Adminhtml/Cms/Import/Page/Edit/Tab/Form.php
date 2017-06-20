<?php

class Cartin24_Cmsimport_Block_Adminhtml_Cms_Import_Page_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('cmsimport_form', array('legend'=>Mage::helper('cmsimport')->__('Item information')));
     
      
      $fieldset->addField('behaviour', 'select', array(
          'label'     => Mage::helper('cmsimport')->__('Import Behavior'),
          'required'  => true,
          'name'      => 'behaviour',
          'values'    => array(
		  
			  array(
                  'value'     => 0,
                  'label'     => Mage::helper('cmsimport')->__('Append Complex Data'),
				  
              ),
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('cmsimport')->__('Replace Existing Complex Data'),
              ),
          ),
	  ));
      
      $fieldset->addField('pagecsv', 'file', array(
          'label'     => Mage::helper('cmsimport')->__('CSV File'),
          'required'  => true,
          'name'      => 'pagecsv',
	  ));
		     
      return parent::_prepareForm();
  }
}
