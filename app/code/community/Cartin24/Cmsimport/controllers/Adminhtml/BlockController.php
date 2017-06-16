<?php
require_once 'Mage/Adminhtml/controllers/Cms/BlockController.php';
class Cartin24_Cmsimport_Adminhtml_BlockController extends Mage_Adminhtml_Cms_BlockController
{	 
	
	public function indexAction() {
		$this->_redirect('adminhtml/cms_block');
	}
	public function uploadAction() {
		 		
			$this->loadLayout();
			$this->_addContent($this->getLayout()->createBlock('cmsimport/adminhtml_cms_import_block_edit'))
				->_addLeft($this->getLayout()->createBlock('cmsimport/adminhtml_cms_import_block_edit_tabs'));
			$this->renderLayout();
	}
	public function exportCsvAction() {
        $fileName   = 'staticblocks.csv';
        $content    = $this->getLayout()->createBlock('cmsimport/adminhtml_cms_import_block_grid')->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);

	}
	public function importAction() {
		if ($data = $this->getRequest()->getPost()) {
			if(isset($_FILES['blockcsv']['name']) && $_FILES['blockcsv']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('blockcsv');
	           		$uploader->setAllowedExtensions(array('csv'));
					$uploader->setAllowRenameFiles(false);
					
					$uploader->setFilesDispersion(false);
							
					$file = new Varien_Io_File();					
					$path = Mage::getBaseDir('var') . DS .'cmsimport'. DS;
					$importReadyDirResult = $file->mkdir($path);				
					$ext = end((explode(".", $name)));
					$fileName = time().'_'.'static_block.csv';
					$uploader->save($path, $fileName );
					
				} catch (Exception $e) {
		      	
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					 $this->_redirect('*/adminhtml_block/upload');
					return;
		        }
				$importModel = Mage::getModel('cmsimport/cmsimport');
			
			try {				
					$result = $importModel->importBlock($fileName,$data['behaviour']);
					
					if($result){
						$res = explode('~',$result);
						if($res[0] > 0) 
							Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__($res[0] .' row(s) are imported.'));
						if($res[1] >0)
							Mage::getSingleton('adminhtml/session')->addError($res[1] .' row(s) are skipped.');	

						$this->_redirect('adminhtml/cms_block');
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
