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
 * Entry collection resource model
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Model_Resource_Entry_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract{
	protected $_joinedFields = array();
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function _construct(){
		parent::_construct();
		$this->_init('autoimport/entry');
		$this->_map['fields']['store'] = 'store_table.store_id';
	}
	/**
	 * get entries as array
	 * @access protected
	 * @param string $valueField
	 * @param string $labelField
	 * @param array $additional
	 * @return array
	 * @author Ultimate Module Creator
	 */
	protected function _toOptionArray($valueField='entity_id', $labelField='title', $additional=array()){
		return parent::_toOptionArray($valueField, $labelField, $additional);
	}
	/**
	 * get options hash
	 * @access protected
	 * @param string $valueField
	 * @param string $labelField
	 * @return array
	 * @author Ultimate Module Creator
	 */
	protected function _toOptionHash($valueField='entity_id', $labelField='title'){
		return parent::_toOptionHash($valueField, $labelField);
	}
	/**
	 * Add filter by store
	 * @access public
	 * @param int|Mage_Core_Model_Store $store
	 * @param bool $withAdmin
	 * @return Evirtual_Autoimport_Model_Resource_Entry_Collection
	 * @author Ultimate Module Creator
	 */
	public function addStoreFilter($store, $withAdmin = true){
		if (!isset($this->_joinedFields['store'])){
			if ($store instanceof Mage_Core_Model_Store) {
				$store = array($store->getId());
			}
			if (!is_array($store)) {
				$store = array($store);
			}
			if ($withAdmin) {
				$store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
			}
			$this->addFilter('store', array('in' => $store), 'public');
			$this->_joinedFields['store'] = true;
		}
		return $this;
	}
	/**
	 * Join store relation table if there is store filter
	 * @access protected
	 * @return Evirtual_Autoimport_Model_Resource_Entry_Collection
	 * @author Ultimate Module Creator
	 */
	protected function _renderFiltersBefore(){
		if ($this->getFilter('store')) {
			$this->getSelect()->join(
				array('store_table' => $this->getTable('autoimport/entry_store')),
				'main_table.entity_id = store_table.entry_id',
				array()
			)->group('main_table.entity_id');
			/*
			 * Allow analytic functions usage because of one field grouping
			 */
			$this->_useAnalyticFunction = true;
		}
		return parent::_renderFiltersBefore();
	}
	/**
	 * Get SQL for get record count.
	 * Extra GROUP BY strip added.
	 * @access public
	 * @return Varien_Db_Select
	 * @author Ultimate Module Creator
	 */
	public function getSelectCountSql(){
		$countSelect = parent::getSelectCountSql();
		$countSelect->reset(Zend_Db_Select::GROUP);
		return $countSelect;
	}
}