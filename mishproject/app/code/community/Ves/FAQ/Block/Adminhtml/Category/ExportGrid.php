<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves FAQ Extension
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FAQ_Block_Adminhtml_Category_ExportGrid extends  Mage_Adminhtml_Block_Widget_Grid{

	public function __construct()
	{
		parent::__construct();
		$this->setId('categoryId');
		$this->setDefaultSort('category_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * set collection for grid
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('ves_faq/category')->getCollection();
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		foreach ($collection as $_category) {
			$results = $query = '';
			$query = 'SELECT store_id FROM ' . $resource->getTableName('ves_faq/category_store').' WHERE category_id = '.$_category->getCategoryId();
			$results = $readConnection->fetchCol($query);
			$_category->setData('stores', implode('-', $results));
		}
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * add column for grid
	 */
	protected function _prepareColumns(){

		$this->addColumn('category_id', array(
			'header' => Mage::helper('ves_faq')->__('category_id'),
			'index'	=> 'category_id',
			));

		$this->addColumn('name', array(
			'header' => Mage::helper('ves_faq')->__('name'),
			'index'	=> 'name',
			));

		$this->addColumn('prefix', array(
			'header' => Mage::helper('ves_faq')->__('prefix'),
			'index'	=> 'prefix',
			));

		$this->addColumn('parent_id', array(
			'header' => Mage::helper('ves_faq')->__('parent_id'),
			'index'	=> 'parent_id',
			));

		$this->addColumn('identifier', array(
			'header' => Mage::helper('ves_faq')->__('identifier'),
			'index'	=> 'identifier',
			));

		$this->addColumn('layout', array(
			'header' => Mage::helper('ves_faq')->__('layout'),
			'index'	=> 'layout',
			));

		$this->addColumn('title', array(
			'header' => Mage::helper('ves_faq')->__('title'),
			'index'	=> 'title',
			));

		$this->addColumn('image', array(
			'header' => Mage::helper('ves_faq')->__('image'),
			'index'	=> 'image',
			));

		$this->addColumn('description', array(
			'header' => Mage::helper('ves_faq')->__('description'),
			'index'	=> 'description',
			));

		$this->addColumn('status', array(
			'header' => Mage::helper('ves_faq')->__('status'),
			'index'	=> 'status',
			));

		$this->addColumn('position', array(
			'header' => Mage::helper('ves_faq')->__('position'),
			'index'	=> 'position',
			));

		$this->addColumn('meta_keywords', array(
			'header' => Mage::helper('ves_faq')->__('meta_keywords'),
			'index'	=> 'meta_keywords',
			));

		$this->addColumn('meta_description', array(
			'header' => Mage::helper('ves_faq')->__('meta_description'),
			'index'	=> 'meta_description',
			));

		$this->addColumn('include_in_sidebar', array(
			'header' => Mage::helper('ves_faq')->__('include_in_sidebar'),
			'index'	=> 'include_in_sidebar',
			));

		$this->addColumn('stores', array(
			'header' => Mage::helper('ves_faq')->__('stores'),
			'index'	=> 'stores',
			));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('category_id');
		$this->getMassactionBlock()->setFormFieldName('category');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'    => Mage::helper('ves_faq')->__('Delete'),
			'url'      => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('ves_faq')->__('Are you sure?')
			));

		$statuses = array(
			1 => Mage::helper('ves_faq')->__('Enabled'),
			2 => Mage::helper('ves_faq')->__('Disabled')
			);

		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('ves_faq')->__('Change status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name' => 'status',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => Mage::helper('ves_faq')->__('Status'),
					'values' => $statuses
					)
				)
			));

		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('ves_faq')->__('Action'),
				'width'     => '100',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('ves_faq')->__('Edit'),
						'url'       => array('base'=> '*/*/edit'),
						'field'     => 'id'
						),
					array(
						'caption'   => Mage::helper('ves_faq')->__('Delete'),
						'url'       => array('base'=> '*/*/delete'),
						'field'     => 'id'
						)
					),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
				));

		return $this;
	}

	/**
     * Helper function to do after load modifications
     *
     */
	protected function _afterLoadCollection()
	{
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}

	/**
	 * Retrive url of row
	 */
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	/**
     * Helper function to add store filter condition
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
     */
	protected function _filterStoreCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
		$this->getCollection()->addStoreFilter($value);
	}
}