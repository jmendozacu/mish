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
class Ves_FAQ_Block_Adminhtml_Answer_Grid extends  Mage_Adminhtml_Block_Widget_Grid{

	public function __construct()
	{
		parent::__construct();
		$this->setId('answerId');
		$this->setDefaultSort('answer_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * set collection for grid
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('ves_faq/answer')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * add column for grid
	 */
	protected function _prepareColumns(){

		$this->addColumn('answer_id', array(
			'header' => Mage::helper('ves_faq')->__('ID'),
			'align'	=> 'left',
			'width'	=> '10px',
			'index'	=> 'answer_id',
			));

		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', 
				array (
					'header' => Mage::helper('cms')->__('Store view'), 
					'index' => 'store_id', 
					'type' => 'store',
					'width' => '200px',
					'store_all' => true,
					'store_view' => true, 
					'sortable' => false, 
					'filter_condition_callback' => array (
						$this, 
						'_filterStoreCondition' ) ));
		}

		$this->addColumn('answer_content', array(
			'header' => Mage::helper('ves_faq')->__('Content'),
			'align'	=> 'left',
			'index'	=> 'answer_content',
			'renderer' => 'ves_faq/adminhtml_renderer_content'
			));

		$this->addColumn('status', array(
			'header' => Mage::helper('ves_faq')->__('Status'),
			'align' => 'left',
			'width' => '80px',
			'index' => 'status',
			'type' => 'options',
			'options' => array(
				1 =>  Mage::helper('ves_faq')->__('Approved'),
				2 =>  Mage::helper('ves_faq')->__('Pending')
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
		
		$this->addExportType('*/*/exportCsv', Mage::helper('ves_faq')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('ves_faq')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() { 
		$this->setMassactionIdField('answer_id');
		$this->getMassactionBlock()->setFormFieldName('answer');
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
		return $this;
	}

	/**
	 * get url of row
	 */
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
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

}