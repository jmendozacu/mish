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
 * Entry admin controller
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Adminhtml_Autoimport_EntryController extends Evirtual_Autoimport_Controller_Adminhtml_Autoimport{
	/**
	 * init the entry
	 * @access protected
	 * @return Evirtual_Autoimport_Model_Entry
	 */
	protected function _initEntry(){
		$entryId  = (int) $this->getRequest()->getParam('id');
		$entry	= Mage::getModel('autoimport/entry');
		if ($entryId) {
			$entry->load($entryId);
		}
		Mage::register('current_entry', $entry);
		return $entry;
	}
	
	public function mappingAction() {
		
		$ulrId=Mage::getModel('core/cookie')->get('entryUrl');
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('autoimport/adminhtml_entry_edit_tab_mapping','admin.entry.mapping')->setUrlId($ulrId)
			->setUseAjax(true)
			->toHtml()
		);
		
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
			 ->_title(Mage::helper('autoimport')->__('Entries'));
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
	 * edit entry - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function editAction() {
		$entryId	= $this->getRequest()->getParam('id');
		$entry  	= $this->_initEntry();
		if ($entryId && !$entry->getId()) {
			$this->_getSession()->addError(Mage::helper('autoimport')->__('This entry no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$entry->setData($data);
		}
		Mage::register('entry_data', $entry);
		$this->loadLayout();
		$this->_title(Mage::helper('autoimport')->__('Autoimport'))
			 ->_title(Mage::helper('autoimport')->__('Entries'));
		if ($entry->getId()){
			$this->_title($entry->getTitle());
		}
		else{
			$this->_title(Mage::helper('autoimport')->__('Add entry'));
		}
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		}
		$this->renderLayout();
	}
	/**
	 * new entry action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function newAction() {
		$this->_forward('edit');
	}
	/**
	 * save entry - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost('entry')) {
			try {
				
				$attributemapping=array();
				$catalogtype=$data['catalogtype'];
				$mappingData=$this->getRequest()->getPost();
				$ProductAttDb=$mappingData['gui_data']['map'][$catalogtype]['db'];
				$ProductAttFile=$mappingData['gui_data']['map'][$catalogtype]['file'];
				array_push($attributemapping,array("db"=>$ProductAttDb,"file"=>$ProductAttFile));
				$data['attributemapping']=serialize($attributemapping);
				$entry = $this->_initEntry();
				$entry->addData($data);
				$entry->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('autoimport')->__('Entry was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $entry->getId()));
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
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was a problem saving the entry.'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Unable to find entry to save.'));
		$this->_redirect('*/*/');
	}
	/**
	 * delete entry - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0) {
			try {
				$entry = Mage::getModel('autoimport/entry');
				$entry->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('autoimport')->__('Entry was successfully deleted.'));
				$this->_redirect('*/*/');
				return; 
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was an error deleteing entry.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				Mage::logException($e);
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Could not find entry to delete.'));
		$this->_redirect('*/*/');
	}
	/**
	 * mass delete entry - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function massDeleteAction() {
		$entryIds = $this->getRequest()->getParam('entry');
		if(!is_array($entryIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Please select entries to delete.'));
		}
		else {
			try {
				foreach ($entryIds as $entryId) {
					$entry = Mage::getModel('autoimport/entry');
					$entry->setId($entryId)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('autoimport')->__('Total of %d entries were successfully deleted.', count($entryIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was an error deleteing entries.'));
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
		$entryIds = $this->getRequest()->getParam('entry');
		if(!is_array($entryIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Please select entries.'));
		} 
		else {
			try {
				foreach ($entryIds as $entryId) {
				$entry = Mage::getSingleton('autoimport/entry')->load($entryId)
							->setStatus($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
				}
				$this->_getSession()->addSuccess($this->__('Total of %d entries were successfully updated.', count($entryIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was an error updating entries.'));
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
		$fileName   = 'entry.csv';
		$content	= $this->getLayout()->createBlock('autoimport/adminhtml_entry_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as MsExcel - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportExcelAction(){
		$fileName   = 'entry.xls';
		$content	= $this->getLayout()->createBlock('autoimport/adminhtml_entry_grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as xml - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportXmlAction(){
		$fileName   = 'entry.xml';
		$content	= $this->getLayout()->createBlock('autoimport/adminhtml_entry_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	
	public function runAction(){
			
			$entryId	= $this->getRequest()->getParam('id');
			$entry	= Mage::getModel('autoimport/entry')->load($entryId);
			Mage::getModel('autoimport/observer')->runEntry($entry);	
			
			$url=Mage::getUrl("admin/autoimport_activiy/index");
			
			echo "<div style='padding:10px; font-size:20px; color:green; border:solid 1px green; margin:10px auto;'>Imported successfully , Please check Activity Log...</div>";
			
	}
	
	
}