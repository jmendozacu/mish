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
class Ves_FAQ_Block_Adminhtml_Question_ExportGrid extends  Mage_Adminhtml_Block_Widget_Grid{

	public function __construct()
	{;
		parent::__construct();
		$this->setId('questionId');
		$this->setDefaultSort('question_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * set collection for grid
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('ves_faq/question')->getCollection();
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		foreach ($collection as $_question) {
			$results = $query = '';
			$query = 'SELECT store_id FROM ' . $resource->getTableName('ves_faq/question_store').' WHERE question_id = '.$_question->getQuestionId();
			$results = $readConnection->fetchCol($query);
			$_question->setData('stores', implode('-', $results));
		}
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * add column for grid
	 */
	protected function _prepareColumns(){

		$this->addColumn('question_id', array(
			'header' => Mage::helper('ves_faq')->__('question_id'),
			'index'	=> 'question_id',
			));

		$this->addColumn('title', array(
			'header' => Mage::helper('ves_faq')->__('title'),
			'index'	=> 'title',
			));

		$this->addColumn('description', array(
			'header' => Mage::helper('ves_faq')->__('description'),
			'index'	=> 'description',
			));

		$this->addColumn('default_answer', array(
			'header' => Mage::helper('ves_faq')->__('default_answer'),
			'index'	=> 'default_answer',
			));

		$this->addColumn('status', array(
			'header' => Mage::helper('ves_faq')->__('status'),
			'index'	=> 'status',
			));

		$this->addColumn('author_name', array(
			'header' => Mage::helper('ves_faq')->__('author_name'),
			'index'	=> 'author_name',
			));

		$this->addColumn('author_email', array(
			'header' => Mage::helper('ves_faq')->__('author_email'),
			'index'	=> 'author_email',
			));

		$this->addColumn('category_id', array(
			'header' => Mage::helper('ves_faq')->__('category_id'),
			'index'	=> 'category_id',
			));

		$this->addColumn('visibility', array(
			'header' => Mage::helper('ves_faq')->__('visibility'),
			'index'	=> 'visibility',
			));

		$this->addColumn('position', array(
			'header' => Mage::helper('ves_faq')->__('position'),
			'index'	=> 'position',
			));

		$this->addColumn('created_at', array(
			'header' => Mage::helper('ves_faq')->__('created_at'),
			'index'	=> 'created_at',
			));

		$this->addColumn('updated_at', array(
			'header' => Mage::helper('ves_faq')->__('updated_at'),
			'index'	=> 'updated_at',
			));

		$this->addColumn('product_id', array(
			'header' => Mage::helper('ves_faq')->__('product_id'),
			'index'	=> 'product_id',
			));

		$this->addColumn('question_type', array(
			'header' => Mage::helper('ves_faq')->__('question_type'),
			'index'	=> 'question_type',
			));

		$this->addColumn('customer_id', array(
			'header' => Mage::helper('ves_faq')->__('customer_id'),
			'index'	=> 'customer_id',
			));

		$this->addColumn('stores', array(
			'header'    => Mage::helper('ves_faq')->__('stores'),
			'index'     => 'stores'
			));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('question_id');
		$this->getMassactionBlock()->setFormFieldName('question');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'    => Mage::helper('ves_faq')->__('Delete'),
			'url'      => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('ves_faq')->__('Are you sure?')
			));

		$statuses = array(
			1 => Mage::helper('ves_faq')->__('Approved'),
			2 => Mage::helper('ves_faq')->__('Pending')
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

		$visibility = array(
			1 => Mage::helper('ves_faq')->__('Public'),
			2 => Mage::helper('ves_faq')->__('Private')
			);

		$this->getMassactionBlock()->addItem('visibility', array(
			'label'=> Mage::helper('ves_faq')->__('Change Visibility'),
			'url'  => $this->getUrl('*/*/massVisibility', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name' => 'visibility',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => Mage::helper('ves_faq')->__('Visibility'),
					'values' => $visibility
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

	/**
	 * get url of row
	 */
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}