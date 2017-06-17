<?php

class VES_BannerManager_Block_Adminhtml_Item_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('bannermanager_form', array('legend'=>Mage::helper('bannermanager')->__('Item information')));
      $fieldset->addField('identifier', 'text', array(
          'label'     => Mage::helper('bannermanager')->__('Identifier'),
          'class'     => 'required-entry validate-identifier',
          'required'  => true,
          'name'      => 'identifier',
      ));
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('bannermanager')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
      
	 $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('bannermanager')->__('Images'),
          'required'  => true,
          'name'      => 'filename',
	  ));
	  /*
	  $fieldset->addField('file_thumbnail', 'image', array(
          'label'     => Mage::helper('bannermanager')->__('Images Thumbnail'),
          'required'  => false,
          'name'      => 'file_thumbnail',
	  ));
	  */
	 $fieldset->addField('banner_id', 'select', array(
          'name'      => 'banner_id',
          'label'     => Mage::helper('bannermanager')->__('Banner'),
          'title'     => Mage::helper('bannermanager')->__('Banner'),
	 	  'required'  => false,
          'values'	  => Mage::getModel('bannermanager/banner')->toOptionArray()
      ));
      
	  $fieldset->addField('url', 'text', array(
          'label'     => Mage::helper('bannermanager')->__('URL'),
          'name'      => 'url',
	  	  /*'class'	  => 'validate-url',*/
      ));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('bannermanager')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bannermanager')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('bannermanager')->__('Disabled'),
              ),
          ),
      ));
      $fieldset->addField('sort_order', 'text', array(
          'label'     => Mage::helper('bannermanager')->__('Sort Order'),
          'name'      => 'sort_order',
	  	  'class'	  => 'validate-number',
      ));
	  

	  
	     
      $fieldset->addField('target_mode', 'select', array(
          'label'     => Mage::helper('bannermanager')->__('Open link in'),
          'name'      => 'target_mode',
          'values'    => array(
			array(
                  'value'     => 0,
                  'label'     => Mage::helper('bannermanager')->__('Same window'),
              ),
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bannermanager')->__('New window'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('bannermanager')->__('Popup'),
              ),
          ),
      ));
	  
	     
      $fieldset->addField('desc_pos', 'select', array(
          'label'     => Mage::helper('bannermanager')->__('Desc Position'),
          'name'      => 'desc_pos',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bannermanager')->__('top'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('bannermanager')->__('right'),
              ),
			   array(
                  'value'     => 3,
                  'label'     => Mage::helper('bannermanager')->__('bottom'),
              ),
			   array(
                  'value'     => 4,
                  'label'     => Mage::helper('bannermanager')->__('left'),
              ),
          ),
      ));
	  
	  	     
      $fieldset->addField('background', 'select', array(
          'label'     => Mage::helper('bannermanager')->__('Desc Background'),
          'name'      => 'background',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bannermanager')->__('light'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('bannermanager')->__('dark'),
              ),
          ),
      ));
	  /*
      $fieldset->addField('from_date', 'date', array(
          'label'     => Mage::helper('bannermanager')->__('Start Date'),
          'required'  => false,
          'name'      => 'from_date',
      	  'format'	  => 'yyyy-MM-dd',
      	  'image'	  => $this->getSkinUrl('images/grid-cal.gif'),
      ));
      $fieldset->addField('to_date', 'date', array(
          'label'     => Mage::helper('bannermanager')->__('End Date'),
          'required'  => false,
          'name'      => 'to_date',
      	  'format'	  => 'yyyy-MM-dd',
      	  'image'	  => $this->getSkinUrl('images/grid-cal.gif'),
      ));
      
      $fieldset->addField('short_description', 'editor', array(
          'name'      => 'short_description',
          'label'     => Mage::helper('bannermanager')->__('Short Description'),
          'title'     => Mage::helper('bannermanager')->__('Short Description'),
          'style'     => 'width:700px; height:200px;',
          'wysiwyg'   => false,
      ));
      */
     $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('bannermanager')->__('Description'),
          'title'     => Mage::helper('bannermanager')->__('Description'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
      ));
      
      if ( Mage::getSingleton('adminhtml/session')->getBannerManagerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBannerManagerData());
          Mage::getSingleton('adminhtml/session')->setBannerManagerData(null);
      } elseif ( Mage::registry('bannermanager_data') ) {
          $form->setValues(Mage::registry('bannermanager_data')->getData());
      }
  	if($bannerId = $this->getRequest()->getParam('banner')){
     	$form->getElement('banner_id')->setValue($bannerId);
     }
      return parent::_prepareForm();
  }
}