<?php
class Cartin24_Cmsimport_Block_Adminhtml_Cms_Import_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid {


	public function __construct()
	{
		parent::__construct();
		$this->setId('staticBlocksGrid');
		$this->setDefaultSort('block_id');
		$this->setDefaultDir('desc');
	}
	protected function _prepareColumns()
	{
	
		$this->addColumn('title', array(
				'header'    =>Mage::helper('cmsimport')->__('Title'),
				'width'     =>'50px',
				'index'     => 'title'
		));

		$this->addColumn('content', array(
				'header'    =>Mage::helper('cmsimport')->__('Content'),
				'width'     =>'50px',
				'index'     => 'content',
				'frame_callback' => array($this, 'htmlentityDecode')
		));

		$this->addColumn('identifier', array(
				'header'    =>Mage::helper('cmsimport')->__('Identifier'),
				'width'     =>'50px',
				'index'     => 'identifier'
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

		$this->addColumn('stores', array(
				'header'        => Mage::helper('cms')->__('Stores'),
				'index'         => 'stores',
		));

		$this->addExportType('cmsimport/adminhtml_block/exportCsv', Mage::helper('cmsimport')->__('CSV'));

		return parent::_prepareColumns();


	}
	protected function _prepareCollection()
	{
		$blockCollection = Mage::getResourceModel('cms/block_collection');

		foreach ($blockCollection as $key => $block) {
			$stores = $block->getResource()->lookupStoreIds($block->getBlockId());
			$stores = implode(';', $stores);
			$block->setStores($stores);
		} 

		$this->setCollection($blockCollection);

		return parent::_prepareCollection();
	}
	public function htmlentityDecode( $value ) {

		$value = html_entity_decode($value);	
		return $value;

	} 
}
