<?php
/**
 * Evirtual_Autoimport extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Evirtual
 * @package		Evirtual_Autoimport
 * @copyright  	Copyright (c) 2013
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Entry admin grid block
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Block_Adminhtml_Entry_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('entryGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	/**
	 * prepare collection
	 * @access protected
	 * @return Evirtual_Autoimport_Block_Adminhtml_Entry_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('autoimport/entry')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	/**
	 * prepare grid collection
	 * @access protected
	 * @return Evirtual_Autoimport_Block_Adminhtml_Entry_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareColumns(){
		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('autoimport')->__('Id'),
			'index'		=> 'entity_id',
			'type'		=> 'number'
		));
		$this->addColumn('title', array(
			'header'=> Mage::helper('autoimport')->__('Title'),
			'index' => 'title',
			'type'	 	=> 'text',

		));
		$this->addColumn('type', array(
			'header'=> Mage::helper('autoimport')->__('Type'),
			'index' => 'type',
			'type'	 	=> 'text',

		));
		$this->addColumn('url', array(
			'header'=> Mage::helper('autoimport')->__('Url'),
			'index' => 'url',
			'type'	 	=> 'text',

		));
		$this->addColumn('status', array(
			'header'	=> Mage::helper('autoimport')->__('Status'),
			'index'		=> 'status',
			'type'		=> 'options',
			'options'	=> array(
				'1' => Mage::helper('autoimport')->__('Enabled'),
				'0' => Mage::helper('autoimport')->__('Disabled'),
			)
		));
		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'=> Mage::helper('autoimport')->__('Store Views'),
				'index' => 'store_id',
				'type'  => 'store',
				'store_all' => true,
				'store_view'=> true,
				'sortable'  => false,
				'filter_condition_callback'=> array($this, '_filterStoreCondition'),
			));
		}
		$this->addColumn('created_at', array(
			'header'	=> Mage::helper('autoimport')->__('Created at'),
			'index' 	=> 'created_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('updated_at', array(
			'header'	=> Mage::helper('autoimport')->__('Updated at'),
			'index' 	=> 'updated_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('action',
			array(
				'header'=>  Mage::helper('autoimport')->__('Action'),
				'width' => '100',
				'type'  => 'action',
				'getter'=> 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('autoimport')->__('Edit'),
						'url'   => array('base'=> '*/*/edit'),
						'field' => 'id'
					),
					array(
						'caption'   => Mage::helper('autoimport')->__('Run'),
						'url'   => array('base'=> '*/*/run'),
						'field' => 'id'
					)
				),
				'filter'=> false,
				'is_system'	=> true,
				'sortable'  => false,
		));
		
		
		
		
		$this->addExportType('*/*/exportCsv', Mage::helper('autoimport')->__('CSV'));
		$this->addExportType('*/*/exportExcel', Mage::helper('autoimport')->__('Excel'));
		$this->addExportType('*/*/exportXml', Mage::helper('autoimport')->__('XML'));
		return parent::_prepareColumns();
	}
	/**
	 * prepare mass action
	 * @access protected
	 * @return Evirtual_Autoimport_Block_Adminhtml_Entry_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareMassaction(){
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('entry');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('autoimport')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('autoimport')->__('Are you sure?')
		));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('autoimport')->__('Change status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'status' => array(
						'name' => 'status',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => Mage::helper('autoimport')->__('Status'),
						'values' => array(
								'1' => Mage::helper('autoimport')->__('Enabled'),
								'0' => Mage::helper('autoimport')->__('Disabled'),
						)
				)
			)
		));
		return $this;
	}
	/**
	 * get the row url
	 * @access public
	 * @param Evirtual_Autoimport_Model_Entry
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	/**
	 * get the grid url
	 * @access public
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getGridUrl(){
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
	/**
	 * after collection load
	 * @access protected
	 * @return Evirtual_Autoimport_Block_Adminhtml_Entry_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _afterLoadCollection(){
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}
	/**
	 * filter store column
	 * @access protected
	 * @param Evirtual_Autoimport_Model_Resource_Entry_Collection $collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
	 * @return Evirtual_Autoimport_Block_Adminhtml_Entry_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _filterStoreCondition($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
        	return;
		}
		$collection->addStoreFilter($value);
		return $this;
    }
}