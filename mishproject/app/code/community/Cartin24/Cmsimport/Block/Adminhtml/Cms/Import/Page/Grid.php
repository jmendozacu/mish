<?php
class Cartin24_Cmsimport_Block_Adminhtml_Cms_Import_Page_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('staticBlocksGrid');
		$this->setDefaultSort('page_id');
		$this->setDefaultDir('desc');
	}

	protected function _prepareColumns()
	{
		
		$this->addColumn('title', array(
				'header'    =>Mage::helper('cmsimport')->__('Title'),
				'width'     =>'50px',
				'index'     => 'title'
		));

		$this->addColumn('root_template', array(
				'header'    =>Mage::helper('cmsimport')->__('Root_Template'),
				'width'     =>'50px',
				'index'     => 'root_template'
		));

		$this->addColumn('meta_keywords', array(
				'header'    =>Mage::helper('cmsimport')->__('Meta_Keywords'),
				'width'     =>'50px',
				'index'     => 'meta_keywords'
		));

		$this->addColumn('meta_description', array(
				'header'    =>Mage::helper('cmsimport')->__('Meta_Description'),
				'width'     =>'50px',
				'index'     => 'meta_description'
		));

		$this->addColumn('identifier', array(
				'header'    =>Mage::helper('cmsimport')->__('Identifier'),
				'width'     =>'50px',
				'index'     => 'identifier'
		));

		$this->addColumn('content_heading', array(
				'header'    =>Mage::helper('cmsimport')->__('Content_Heading'),
				'width'     =>'50px',
				'index'     => 'content_heading',
				'frame_callback' => array($this, 'htmlentityDecode')
		));

		$this->addColumn('content', array(
				'header'    =>Mage::helper('cmsimport')->__('Content'),
				'width'     =>'50px',
				'index'     => 'content',
				'frame_callback' => array($this, 'htmlentityDecode')
		));

		$this->addColumn('creation_time', array(
				'header'    =>Mage::helper('cmsimport')->__('Date_Created'),
				'width'     =>'50px',
				'index'     => 'creation_time'
		));

		$this->addColumn('update_time', array(
				'header'    =>Mage::helper('cmsimport')->__('Last_Updated'),
				'width'     =>'50px',
				'index'     => 'update_time'
		));

		$this->addColumn('is_active', array(
				'header'    =>Mage::helper('cmsimport')->__('Is_Active'),
				'width'     =>'50px',
				'index'     => 'is_active'
		));

		$this->addColumn('sort_order', array(
				'header'    =>Mage::helper('cmsimport')->__('Sort_Order'),
				'width'     =>'50px',
				'index'     => 'sort_order'
		));


		$this->addColumn('layout_update_xml', array(
				'header'    =>Mage::helper('cmsimport')->__('Layout_Update_XML'),
				'width'     =>'50px',
				'index'     => 'layout_update_xml',
				'frame_callback' => array($this, 'htmlentityDecode')
				
		));


		$this->addColumn('custom_theme', array(
				'header'    =>Mage::helper('cmsimport')->__('Custom_Theme'),
				'width'     =>'50px',
				'index'     => 'custom_theme'
		));

		$this->addColumn('custom_root_template', array(
				'header'    =>Mage::helper('cmsimport')->__('Custom_Root_Template'),
				'width'     =>'50px',
				'index'     => 'custom_root_template'
		));

		$this->addColumn('custom_layout_update_xml', array(
				'header'    =>Mage::helper('cmsimport')->__('Custom_Layout_Update_XML'),
				'width'     =>'50px',
				'index'     => 'custom_layout_update_xml',
				'frame_callback' => array($this, 'htmlentityDecode')
		));

		$this->addColumn('custom_theme_from', array(
				'header'    =>Mage::helper('cmsimport')->__('Custom_Theme_From'),
				'width'     =>'50px',
				'index'     => 'custom_theme_from'
		));

		$this->addColumn('custom_theme_to', array(
				'header'    =>Mage::helper('cmsimport')->__('Custom_Theme_To'),
				'width'     =>'50px',
				'index'     => 'custom_theme_to'
		));

		$this->addColumn('stores', array(
				'header'        => Mage::helper('cms')->__('Stores'),
				'index'         => 'stores',
		));

		$this->addExportType('cmsimport/adminhtml_page/exportCsv', Mage::helper('cmsimport')->__('CSV'));

		return parent::_prepareColumns();


	}

	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('cms/page_collection');

		foreach ($collection as $key => $page) {
			$stores = $page->getResource()->lookupStoreIds($page->getPageId());
			$stores = implode(';', $stores);
			$page->setStores($stores);
		} 

		$this->setCollection($collection);

		return parent::_prepareCollection();
	}
	public function htmlentityDecode( $value ) {

		$value = html_entity_decode($value);	
		return $value;

	} 


}
