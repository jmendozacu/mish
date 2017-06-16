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
 * Activity admin controller
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Adminhtml_Autoimport_ActiviyController extends Evirtual_Autoimport_Controller_Adminhtml_Autoimport{
	/**
	 * init the activiy
	 * @access protected
	 * @return Evirtual_Autoimport_Model_Activiy
	 */
	protected function _initActiviy(){
		$activiyId  = (int) $this->getRequest()->getParam('id');
		$activiy	= Mage::getModel('autoimport/activiy');
		if ($activiyId) {
			$activiy->load($activiyId);
		}
		Mage::register('current_activiy', $activiy);
		return $activiy;
	}
 	/**
	 * default action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function indexAction() {
		$this->loadLayout();
		$this->_title(Mage::helper('autoimport')->__('Autoimport'))
			 ->_title(Mage::helper('autoimport')->__('Activites'));
		$this->renderLayout();
	}
	/**
	 * grid action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function gridAction() {
		$this->loadLayout()->renderLayout();
	}
	/**
	 * edit activity - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function editAction() {
		$activiyId	= $this->getRequest()->getParam('id');
		$activiy  	= $this->_initActiviy();
		if ($activiyId && !$activiy->getId()) {
			$this->_getSession()->addError(Mage::helper('autoimport')->__('This activity no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$activiy->setData($data);
		}
		Mage::register('activiy_data', $activiy);
		$this->loadLayout();
		$this->_title(Mage::helper('autoimport')->__('Autoimport'))
			 ->_title(Mage::helper('autoimport')->__('Activites'));
		if ($activiy->getId()){
			$this->_title($activiy->getTitle());
		}
		else{
			$this->_title(Mage::helper('autoimport')->__('Add activity'));
		}
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		}
		$this->renderLayout();
	}
	/**
	 * new activity action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function newAction() {
		$this->_forward('edit');
	}
	/**
	 * save activity - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost('activiy')) {
			try {
				$activiy = $this->_initActiviy();
				$activiy->addData($data);
				$activiy->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('autoimport')->__('Activity was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $activiy->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} 
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			catch (Exception $e) {
				Mage::logException($e);
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was a problem saving the activity.'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Unable to find activity to save.'));
		$this->_redirect('*/*/');
	}
	/**
	 * delete activity - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0) {
			try {
				$activiy = Mage::getModel('autoimport/activiy');
				$activiy->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('autoimport')->__('Activity was successfully deleted.'));
				$this->_redirect('*/*/');
				return; 
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was an error deleteing activity.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				Mage::logException($e);
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Could not find activity to delete.'));
		$this->_redirect('*/*/');
	}
	/**
	 * mass delete activity - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function massDeleteAction() {
		$activiyIds = $this->getRequest()->getParam('activiy');
		if(!is_array($activiyIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Please select activites to delete.'));
		}
		else {
			try {
				foreach ($activiyIds as $activiyId) {
					$activiy = Mage::getModel('autoimport/activiy');
					$activiy->setId($activiyId)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('autoimport')->__('Total of %d activites were successfully deleted.', count($activiyIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was an error deleteing activites.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * mass status change - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function massStatusAction(){
		$activiyIds = $this->getRequest()->getParam('activiy');
		if(!is_array($activiyIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Please select activites.'));
		} 
		else {
			try {
				foreach ($activiyIds as $activiyId) {
				$activiy = Mage::getSingleton('autoimport/activiy')->load($activiyId)
							->setStatus($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
				}
				$this->_getSession()->addSuccess($this->__('Total of %d activites were successfully updated.', count($activiyIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was an error updating activites.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * export as csv - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportCsvAction(){
		$fileName   = 'activiy.csv';
		$content	= $this->getLayout()->createBlock('autoimport/adminhtml_activiy_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as MsExcel - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportExcelAction(){
		$fileName   = 'activiy.xls';
		$content	= $this->getLayout()->createBlock('autoimport/adminhtml_activiy_grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as xml - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportXmlAction(){
		$fileName   = 'activiy.xml';
		$content	= $this->getLayout()->createBlock('autoimport/adminhtml_activiy_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}
}