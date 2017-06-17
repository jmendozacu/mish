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
class Ves_FAQ_Model_Mysql4_Question extends Mage_Core_Model_Mysql4_Abstract
{

	public function _construct()
	{
		$this->_init('ves_faq/question', 'question_id');
	}

	/**
     * Assign page to store views
     *
     * @param Mage_Core_Model_Abstract $object
     */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		$condition = $this->_getWriteAdapter()->quoteInto('question_id = ?', $object->getId());
      // process faq item to store relation
		$this->_getWriteAdapter()->delete($this->getTable('ves_faq/question_store'), $condition);
		$stores = $object->getData('stores');
		if($stores){
			foreach ((array) $object->getData('stores') as $store) {
				$storeArray = array ();
				$storeArray['question_id'] = $object->getId();
				$storeArray['store_id'] = $store;
				$this->_getWriteAdapter()->insert(
					$this->getTable('ves_faq/question_store'), $storeArray
					);
			} 
		}else{
			$stores = $object->getStoreId();
			if($stores) {
				foreach ((array) $stores as $store) {
					$storeArray = array ();
					$storeArray['question_id'] = $object->getId();
					$storeArray['store_id'] = $store;
					$this->_getWriteAdapter()->insert(
						$this->getTable('ves_faq/question_store'), $storeArray
						);
				}
			}

		}
		return parent::_afterSave($object);
	}

   /**
   * Do store and category processing after loading
   * 
   * @param Mage_Core_Model_Abstract $object Current faq item
   */
   protected function _afterLoad(Mage_Core_Model_Abstract $object)
   {
      // process faq item to store relation
   	$select = $this->_getReadAdapter()->select()->from(
   		$this->getTable('ves_faq/question_store')
   		)->where('question_id = ?', $object->getId());

   	if ($data = $this->_getReadAdapter()->fetchAll($select)) {
   		$storesArray = array ();
   		foreach ($data as $row) {
   			$storesArray[] = $row['store_id'];
   		}
   		$object->setData('store_id', $storesArray);
   	}

   	$object->setData('answers',$this->getListAnswer($object->getId()));

   	return parent::_afterLoad($object);
   }

   /**
	 * Retrive list answer
	 * @param  [int] $questionId
	 */
	protected function getListAnswer($questionId){
		$collection = Mage::getModel('ves_faq/answer')->getCollection()
		->addFieldToFilter('question_id',array('in'=>$questionId))
		->addFieldToFilter('status', array('eq'=>1))
		->addStoreFilter()
		->setOrder('answer_id','ASC');
		return $collection;
	}
}