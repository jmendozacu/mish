<?php

class VES_VendorsCategory_Block_Vendor_Category_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_category;

    public function _construct() {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = Mage::registry('category');
        }
        return $this->_category;
    }

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
        $form->setDataObject($this->getCategory());

		$this->setForm($form);
		$fieldset = $form->addFieldset('category_meta_form', array('legend'=>Mage::helper('vendorscategory')->__('Meta Data'),'class'=>'fieldset-wide'));
		$fieldset->addField('meta_title', 'text', array(
			'label'     => Mage::helper('vendorscategory')->__('Page Title'),
			'name'      => 'meta_title',
		));
		$fieldset->addField('meta_keyword', 'textarea', array(
			'label'     => Mage::helper('vendorscategory')->__('Meta Keyword'),
			'class'     => 'textarea',
			'name'      => 'meta_keyword',
		));
		$fieldset->addField('meta_description', 'textarea', array(
			'label'     => Mage::helper('vendorscategory')->__('Meta Description'),
			'class'     => 'textarea',
			'name'      => 'meta_description',
		));

        $form->addValues($this->getCategory()->getData());

		/*if ( Mage::getSingleton('vendors/session')->getFormData() )
		{
			$form->setValues(Mage::getSingleton('vendors/session')->getFormData());
			Mage::getSingleton('vendors/session')->setFormData(null);
		} elseif ( Mage::registry('category_data') ) {
			$form->setValues(Mage::registry('category_data')->getData());
		}*/
		return parent::_prepareForm();
	}
}