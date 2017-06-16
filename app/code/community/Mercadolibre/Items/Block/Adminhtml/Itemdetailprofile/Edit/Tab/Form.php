<?php

class Mercadolibre_Items_Block_Adminhtml_Itemdetailprofile_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{


    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

    }

   protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', Mage::helper('items')-> getMlDefaultStoreId());
        return Mage::app()->getStore($storeId);
    }

  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
	  $fieldset = $form->addFieldset('itemdetailprofile_form', array('legend'=>Mage::helper('items')->__('Template information')));
	  $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId(), 'files_browser_window_url'=> $this->getBaseUrl().'admin/cms_wysiwyg_images/index/')
        );
		
        $contentField = $fieldset->addField('profile_name', 'text', array(
           'label'     => Mage::helper('items')->__('Profile Name'),  
            'name'      => 'profile_name',
            'required'  => true,
			'index'     => 'profile_name',
		    'class'     => 'required-entry',	
        ));
		$contentField = $fieldset->addField('description_header', 'editor', array(
            'label'     => Mage::helper('items')->__('Description Header'), 
			'value'     => 'description_header',
            'state'     => 'html',
		    'name'      => 'description_header',
            'style'     => 'width:55em;height:20em;',
			'class'     => 'required-entry',
			'required'  => true,
			'index'     => 'content',
		    'wysiwyg'   => true,
			'config'    => $wysiwygConfig,
        ));
	
	  if ( Mage::getSingleton('adminhtml/session')->getItemdetailprofileData() )
      {

		  $form->setValues(Mage::getSingleton('adminhtml/session')->getItemdetailprofileData());
          Mage::getSingleton('adminhtml/session')->getItemdetailprofileData(null);
      } elseif ( Mage::registry('itemdetailprofile') ) {
          $form->setValues(Mage::registry('itemdetailprofile')->getData());
      }
	  
      return parent::_prepareForm();
  }
}





