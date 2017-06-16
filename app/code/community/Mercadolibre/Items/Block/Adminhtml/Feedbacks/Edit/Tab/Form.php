<?php

class Mercadolibre_Items_Block_Adminhtml_Feedbacks_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
		$orderIdParam = Mage::app()->getRequest()->getParam('orderid');
		Mage::getSingleton('core/session')->setOrderIdParam($orderIdParam);
        $fieldset = $form->addFieldset('items_form', array('legend' => Mage::helper('items')->__('Feedback information')));
		
		if($orderIdParam == ""){
              
			$fieldset->addField('order_id', 'text', array(
				'label' => Mage::helper('items')->__('Order ID'),
				'class' => 'required-entry',
				'required' => true,
				'readonly' => true,
				'name' => 'order_id',
			));
			
			$fieldset->addField('rating', 'text', array(
				'label' => Mage::helper('items')->__('Rating'),
				'class' => 'required-entry',
				'required' => true,
				'readonly' => true,
				'name' => 'rating',
			)); 
			$fieldset->addField('reason', 'editor', array(
				'name' => 'answer',
				'label' => Mage::helper('items')->__('Reason'),
				'title' => Mage::helper('items')->__('Reason'),
				'style' => 'resize: none; width:600px; height:200px;',
				'readonly'=>false,
				'wysiwyg' => false,
				'required' => true,
			));
	
			$fieldset->addField('message', 'editor', array(
				'name' => 'answer',
				'label' => Mage::helper('items')->__('Message'),
				'title' => Mage::helper('items')->__('Message'),
				'style' => 'resize: none; width:600px; height:200px;',
				'readonly'=>true,
				'wysiwyg' => false,
				'required' => true,
			));
			$fieldset->addField('id', 'hidden', array(
            'label' => Mage::helper('items')->__('ID'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'id',
        	));
	 }
	 	 
        $fieldset->addField('reply', 'editor', array(
            'name' => 'reply',
            'label' => Mage::helper('items')->__('Your Feedback'),
            'title' => Mage::helper('items')->__('Your Feedback'),
            'style' => 'resize: none; width:600px; height:200px;',
            'wysiwyg' => false,
            'required' => true,
        ));
		
		$options = array(
			'positive' => $this->__('Positive'),
			'neutral' => $this->__('neutral'),
			'negative' => $this->__('negative'),
		);

        $fieldset->addField('rating_seller', 'select', array(
                    'name'  => 'rating_seller',
                    'label' => Mage::helper('adminhtml')->__('Seller\'s Rating'),
                    'title' => Mage::helper('adminhtml')->__('Seller\'s Rating'),
                    //'required' => true,
                    'values' => $options,
                    'value' => 'default',
                )
        );
        
        if (Mage::getSingleton('adminhtml/session')->getItemsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getItemsData());
            Mage::getSingleton('adminhtml/session')->setItemsData(null);
        } elseif (Mage::registry('feedbacks')) {
            $form->setValues(Mage::registry('feedbacks')->getData());
        }
        return parent::_prepareForm();
    }

}