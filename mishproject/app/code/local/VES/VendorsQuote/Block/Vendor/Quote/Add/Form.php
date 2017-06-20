<?php

class VES_VendorsQuote_Block_Vendor_Quote_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/prepareQuote', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   ));

      $form->setUseContainer(true);
      $fieldset = $form->addFieldset('vendorsquote_new_form', array('legend'=>Mage::helper('vendorsquote')->__('Customer Information'),'class'=>'fieldset-wide'));
      
      $fieldset->addField('firstname','text', array(
          'label'		=> Mage::helper('vendorsquote')->__('First Name'),
          'required'    => true,
          'name'        => 'firstname',
      ));
      
      $fieldset->addField('lastname','text', array(
          'label'		=> Mage::helper('vendorsquote')->__('Last Name'),
          'required'    => true,
          'name'        => 'lastname',
      ));
      
      $fieldset->addField('email','text', array(
          'label'		=> Mage::helper('vendorsquote')->__('Email'),
          'required'    => true,
          'name'        => 'email',
      ));
      
      if($this->showField('account_detail_telephone')){
          $fieldset->addField('telephone','text', array(
              'label'		=> Mage::helper('vendorsquote')->__('Telephone'),
              'required'    => $this->isRequiredField($this->showField('account_detail_telephone')),
              'name'        => 'telephone',
          ));
      }
      
      
      if($this->showField('account_detail_company')){
          $fieldset->addField('company','text', array(
              'label'		=> Mage::helper('vendorsquote')->__('Company'),
              'required'    => $this->isRequiredField($this->showField('account_detail_company')),
              'name'        => 'company',
          ));
      }
      
      if($this->showField('account_detail_taxvat')){
          $fieldset->addField('taxvat','text', array(
              'label'		=> Mage::helper('vendorsquote')->__('VAT/Tax Id'),
              'required'    => $this->isRequiredField($this->showField('account_detail_taxvat')),
              'name'        => 'taxvat',
          ));
      }
      $this->setForm($form);
      return parent::_prepareForm();
  }

    /**
     * Get show field config
     */
    function showField($field){
        return Mage::helper('vendorsquote')->getConfig($field);
    }
    
    /**
     * Check if a field is required based on showing configuration.
     * @param int $value
     * @return boolean
     */
    public function isRequiredField($value){
        return $value == 2;
    }
}