<?php

class VES_VendorsReview_Block_Adminhtml_Review_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$review = Mage::registry('review_data');
		$customer = Mage::getModel('customer/customer')->load($review->getCustomerId());
		$vendor = Mage::getModel('vendors/vendor')->load($review->getVendorId());
		$statuses = Mage::getModel('vendorsreview/type')->toOptionArray();
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		//rating title fieldset
		$fieldset = $form->addFieldset('review_form', array('legend'=>Mage::helper('vendorsreview')->__('Review details'),'class'=>'fieldset-wide'));
	 	
		//customer
		if(Mage::registry('useAdminMode')) {
			$fieldset->addField('customer_name', 'note', array(
					'label'     => Mage::helper('vendorsreview')->__('Customer'),
					'text'      => '<a href="' . $this->getUrl('*/customer/edit', array('id' => $customer->getId())) . '" onclick="this.target=\'blank\'">' . $customer->getName() . '</a>'
			));
		}
		$fieldset->addField('summary_rating', 'note', array(
				'label'     => Mage::helper('vendorsreview')->__('Summary Rating'),
				'text'      => $this->getLayout()->createBlock('vendorsreview/adminhtml_review_rating_summary')->toHtml(),
		));
		
		if(Mage::registry('useAdminMode')) {
			$fieldset->addField('detailed_rating', 'note', array(
					'label'     => Mage::helper('vendorsreview')->__('Detailed Rating'),
					'required'  => true,
					'class'		=> 'required-entry',
					'text'      => '<div id="rating_detail">'
					. $this->getLayout()->createBlock('vendorsreview/adminhtml_review_rating_detailed')->toHtml()
					. '</div>',
			));
		} else {
			$fieldset->addField('detailed_rating', 'note', array(
					'label'     => Mage::helper('vendorsreview')->__('Detailed Rating'),
					'required'  => false,
					'readonly'	=> true,
					'class'		=> 'required-entry',
					'text'      => '<div id="rating_detail">'
					. $this->getLayout()->createBlock('vendorsreview/adminhtml_review_rating_detailed')->toHtml()
					. '</div>',
			));
		}
		
		/***********************status***********************/
		if(Mage::registry('useAdminMode')) {
			$fieldset->addField('status', 'select', array(
	          'label'     => Mage::helper('vendorsreview')->__('Status'),
	          'name'      => 'status',
			  'require'	  => true,
			  'class'	  => 'required-entry',
	          'values'    => VES_VendorsReview_Model_Type::toOptionArray()
	      ));
		}
		
		/***************************nick name******************/
		$nick_name = array(
				'label'     => Mage::helper('vendorsreview')->__('Nick name'),
				'name'      => 'nick_name',
		);
		if(Mage::registry('useAdminMode')) {
			$nick_name['required'] = true;
			$nick_name['class'] = 'required-entry';
		} else {
			$nick_name['readonly'] = true;
		}
		
		/***********************title************************/
		$title = array(
				'label'     => Mage::helper('vendorsreview')->__('Summary of review'),
				'name'      => 'title',
		);
		if(Mage::registry('useAdminMode')) {
			$title['required'] = true;
			$title['class'] = 'required-entry';
		} else {
			$title['readonly'] = true;
			$title['disabled'] = true;
		}
		
		$fieldset->addField('title', 'text', $title);

		/**************************detail************************/
		
		$detail = array(
				'name'      => 'detail',
				'label'     => Mage::helper('vendorsreview')->__('Review'),
				'title'     => Mage::helper('vendorsreview')->__('Review'),
				'wysiwyg'   => false,
		);
		if(Mage::registry('useAdminMode')) {
			$detail['required'] = true;
			$detail['class'] = 'required-entry';
		} else {
			$detail['readonly'] = true;
			$detail['disabled'] = true;
		}
		
		$fieldset->addField('detail', 'editor',$detail);

		
		
		if ( Mage::getSingleton('vendors/session')->getFormData() )
		{
			$form->setValues(Mage::getSingleton('vendors/session')->getFormData());
			Mage::getSingleton('vendors/session')->setFormData(null);
		} elseif ( Mage::registry('review_data') ) {
			$form->setValues(Mage::registry('review_data')->getData());
		}
		return parent::_prepareForm();
	}
}