<?php

class VES_Vendors_Block_Adminhtml_Vendors_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('vendors_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vendors')->__('Vendor Information'));
	}
	
	protected function _beforeToHtml(){
		Mage::dispatchEvent('ves_vendors_prepare_tabs_before',array('tabs'=>$this));
		$this->addTab('main_section', array(
			'label'     => Mage::helper('vendors')->__('Main'),
			'title'     => Mage::helper('vendors')->__('Main'),
			'content'   => $this->getLayout()->createBlock('vendors/adminhtml_vendors_edit_tab_main')->toHtml(),
		));
	
		$this->addTab('info_section', array(
			'label'     => Mage::helper('vendors')->__('Information'),
			'title'     => Mage::helper('vendors')->__('Information'),
			'content'   => $this->getLayout()->createBlock('vendors/adminhtml_vendors_edit_tab_info')->toHtml(),
		));

		$this->addTab('terms_section', array(
			'label'     => Mage::helper('vendors')->__('Terms'),
			'title'     => Mage::helper('vendors')->__('Terms'),
			'content'   => $this->getLayout()->createBlock('vendors/adminhtml_vendors_edit_tab_terms')->toHtml(),
		));

		$this->addTab('category_section', array(
			'label'     => Mage::helper('vendors')->__('Category'),
			'title'     => Mage::helper('vendors')->__('Category'),
			'content'   => $this->getCategories(),//$this->getLayout()->createBlock('vendors/adminhtml_vendors_edit_tab_category')->toHtml(),
		));

		$this->addTab('bank_section', array(
			'label'     => Mage::helper('vendors')->__('Bank Data'),
			'title'     => Mage::helper('vendors')->__('Bank Data'),
			'content'   => $this->getLayout()->createBlock('vendors/adminhtml_vendors_edit_tab_bank')->toHtml(),
		));

		Mage::dispatchEvent('ves_vendors_prepare_tabs_after',array('tabs'=>$this));
		return parent::_beforeToHtml();
	}


	public function getCategories(){

		$rootCatId = 2;//Mage::app()->getStore()->getRootCategoryId();		

		$html = '<div id="vendors_tabs_category_section_content" style="">
			    <div class="entry-edit">
				<div class="entry-edit-head">
				    <h4 class="icon-head head-edit-form fieldset-legend">Categories</h4>
				    <div class="form-buttons"></div>
				</div>
				<div class="fieldset " id="vendors_form_main">
				    <div class="hor-scroll">
					<table class="form-list" cellspacing="0">
					    <tbody>
						<tr>
						    <td class="label"><label for="categories">Categories : </label></td>
						    <td class="value">'.$this->getTreeCategories($rootCatId, true).'</td>
						</tr>
					    </tbody>
					</table>
				    </div>
				</div>
			    </div>
			</div><style>
            .sub-cat-list.inactive{display: none;}
            .sub-cat-list.active{display: block !important;}
            .sub-cat-list.active {margin-left: 50px;}
        </style>';
		return $html;
	}

	public function getTreeCategories($parentId, $isChild) {
		$categories = $this->selectedCategory();
	    $allCats = Mage::getModel('catalog/category')->getCollection()
		    ->addAttributeToSelect('*')
		    ->addAttributeToFilter('is_active', '1')
	//                            ->addAttributeToFilter('include_in_menu', '1')
		    ->addAttributeToFilter('level', array('gteq' => 2))
		    ->addAttributeToFilter('level', array('lteq' => 3))
		    ->addAttributeToFilter('parent_id', array('eq' => $parentId))
		    ->addAttributeToSort('position', 'asc');

	    $class = (empty($isChild)) ? "sub-cat-list" : "cat-list";
	    $activeClass = (in_array($parentId, $categories) ? " active" : " inactive") ;
	    //$style = (in_array($parentId, $categories) ? " style='margin-left: 50px' " : "");
	    $html .= '<ul class="' . $class . $activeClass .'" >';
	    foreach ($allCats as $category) {
		$html .= '<li><label for="category_' . $category->getId() . '"><input type="checkbox" disabled="disabled" name="category_changer[]" value="' . $category->getId() . '" ' . (in_array($category->getId(), $categories) ? "checked=checked" : "") . ' id="category_' . $category->getId() . '">' . $category->getName() . "</label>";
		$subcats = $category->getChildren();
		if ($subcats != '') {
		    $html .= $this->getTreeCategories($category->getId(), false);
		}
		$html .= '</li>';
	    }
	    $html .= '</ul>';
	    return $html;
	}

	public function selectedCategory(){
		$model = Mage::getModel('vendors/additional')->load($this->getRequest()->getParam('id'),'vendor_id');
		return explode(",",$model->getCategories());
	}

}
