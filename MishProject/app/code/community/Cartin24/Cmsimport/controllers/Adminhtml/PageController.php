<?php
require_once 'Mage/Adminhtml/controllers/Cms/PageController.php';
class Cartin24_Cmsimport_Adminhtml_PageController extends Mage_Adminhtml_Cms_PageController {


	public function exportCsvAction() {
        $fileName   = 'cms_pages.csv';
        $content    = $this->getLayout()->createBlock('cmsimport/adminhtml_cms_import_page_grid')->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);

	}
	public function uploadAction() {
		 		
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('cmsimport/adminhtml_cms_import_page_edit'))
			->_addLeft($this->getLayout()->createBlock('cmsimport/adminhtml_cms_import_page_edit_tabs'));
		$this->renderLayout();
	}
	public function indexAction() {
		$this->_redirect('adminhtml/cms_page');
	}
	
	public function importAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['pagecsv']['name']) && $_FILES['pagecsv']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('pagecsv');
	           		$uploader->setAllowedExtensions(array('csv'));
					$uploader->setAllowRenameFiles(false);
					
					$uploader->setFilesDispersion(false);
							
					$file = new Varien_Io_File();					
					$path = Mage::getBaseDir('var') . DS .'cmsimport'. DS;
					$importReadyDirResult = $file->mkdir($path);				
					
					$fileName = time().'_'.'cms_page.csv';
					$uploader->save($path, $fileName );
					
				} catch (Exception $e) {
		      	
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					 $this->_redirect('*/adminhtml_page/upload');
					return;
		        }
				$importModel = Mage::getModel('cmsimport/cmsimport');
			
			try {				
					 $result = $importModel->importPage($fileName,$data['behaviour']);
					if($result){
						$res = explode('~',$result);
						if($res[0] > 0) 
							Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__($res[0] .' row(s) are imported.'));
						if($res[1] >0)
							Mage::getSingleton('adminhtml/session')->addError($res[1] .' row(s) are skipped. Please check the URL key.');	

						$this->_redirect('adminhtml/cms_page');
					}
					else{
						Mage::getSingleton('adminhtml/session')->addError('File is totally invalid. Please fix errors and re-upload file.');	
						$this->_redirect('*/*/');
					}
		        } catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					$this->_redirect('*/*/');
					return;
				}
			} 
			
			
		}		
		
	}

}
