<?php

class VES_BannerManager_Block_Adminhtml_Item_Edit_Tab_Customhtml extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('bannermanager_form', array('legend'=>Mage::helper('bannermanager')->__('Custom Html Banner Item')));
     
      
      $fieldset->addField('custom_html', 'editor', array(
          'name'      => 'custom_html',
          'label'     => Mage::helper('bannermanager')->__('Custom Html'),
          'title'     => Mage::helper('bannermanager')->__('custom_html'),
          'style'     => 'width:700px; height:400px;',
          'wysiwyg'   => false,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getBannerManagerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBannerManagerData());
          Mage::getSingleton('adminhtml/session')->setBannerManagerData(null);
      } elseif ( Mage::registry('bannermanager_data') ) {
          $form->setValues(Mage::registry('bannermanager_data')->getData());
      }
      return parent::_prepareForm();
  }
}