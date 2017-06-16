<?php

class VES_VendorsCategory_Block_Vendor_Category_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
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

    public function getPath() {
        $category = $this->getCategory();
        if($category->getId()) {
            $parentIds = $category->getAllParentCategoryIds($category);$parentIds = array_reverse($parentIds);
            $parentIds[] = $category->getId();
            return implode('/',$parentIds);
        }
        return '';
    }

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
        $form->setDataObject($this->getCategory());
		$this->setForm($form);
		$fieldset = $form->addFieldset('vendors_form', array('legend'=>Mage::helper('vendorscategory')->__('Category information'),'class'=>'fieldset-wide'));
	 
		$fieldset->addField('name', 'text', array(
			'label'     => Mage::helper('vendorscategory')->__('Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'name',
		));

        if(!$this->getCategory()->getId()) {
            $parentId = $this->getRequest()->getParam('parent');
            $fieldset->addField('path', 'hidden', array(
                'label'     => Mage::helper('vendorscategory')->__('path'),
                'name'      => 'path',
                'value'     => ($parentId)?$parentId:0,
            ));
        } else {
            $fieldset->addField('cid', 'hidden', array(
                'label'     => Mage::helper('vendorscategory')->__('id'),
                'name'      => 'cid',
                'value'     => $this->getCategory()->getId(),
            ));

            $fieldset->addField('path', 'hidden', array(
                'label'     => Mage::helper('vendorscategory')->__('path'),
                'name'      => 'path',
                'value'     => ($this->getPath())?$this->getPath():'',
            ));
        }




		$excludeIds = array();
		if($currentCategory = Mage::registry('category_data')){
			$excludeIds = array(Mage::registry('category_data')->getId());
			$parentCategoryIds = $currentCategory->getChildrenCategoryCollection()->getAllIds();
			$excludeIds = array_merge($excludeIds,$parentCategoryIds);
		}
		/*$fieldset->addField('parent_category_id', 'select', array(
			'label'     => Mage::helper('vendorscategory')->__('Parent Category'),
			'values'	=> Mage::getModel('vendorscategory/source_category')->toOptionArray(Mage::getSingleton('vendors/session')->getVendorId(),true,$excludeIds),
			'name'      => 'parent_category_id',
		));*/
		$fieldset->addField('is_active', 'select', array(
			'label'     => Mage::helper('vendorscategory')->__('Is Active'),
			'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			'name'      => 'is_active',
		));
        /**
         * sort order auto generate
         */
        /*$fieldset->addField('sort_order', 'text', array(
                'label'     => Mage::helper('vendorscategory')->__('Sort order'),
                'name'      => 'sort_order',
                'required'  => true,
        ));*/
        $fieldset->addField('url_key', 'text', array(
			'label'     => Mage::helper('vendorscategory')->__('Url Key'),
			'name'      => 'url_key',
		));
		$fieldset->addField('description', 'textarea', array(
			'label'     => Mage::helper('vendorscategory')->__('Description'),
			'class'     => 'textarea',
			'name'      => 'description',
		));
		$fieldset->addField('page_layout', 'select', array(
			'label'     => Mage::helper('vendorscategory')->__('Page Layout'),
			'values'	=> Mage::getModel('vendorscategory/source_layout')->toOptionArray(),
			'name'      => 'page_layout',
		));
		$fieldset->addField('is_hide_product', 'select', array(
			'label'     => Mage::helper('vendorscategory')->__('Hide All Products'),
			'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			'name'      => 'is_hide_product',
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