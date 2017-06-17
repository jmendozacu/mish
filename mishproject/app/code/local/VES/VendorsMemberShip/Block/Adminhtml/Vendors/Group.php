<?php
class VES_VendorsMemberShip_Block_Adminhtml_Vendors_Group extends VES_Vendors_Block_Adminhtml_Vendors_Group_Edit_Tab_Main{
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendors')->__('Vendor information')));
     
      $fieldset->addField('vendor_group_code', 'text', array(
          'label'     => Mage::helper('vendors')->__('Group Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'vendor_group_code',
      ));

      $fieldset->addField('priority', 'text', array(
          'label'     => Mage::helper('vendors')->__('Priority'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'priority',
      ));
      
      
      if ( Mage::getSingleton('adminhtml/session')->getVendorsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsData());
          Mage::getSingleton('adminhtml/session')->setVendorsData(null);
      } elseif ( Mage::registry('group_data') ) {
          $form->setValues(Mage::registry('group_data')->getData());
      }
      
      return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
    }
}
