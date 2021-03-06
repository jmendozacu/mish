<?php

class Bluethink_Btslider_Adminhtml_BtsliderController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('btslider/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function saveBanerimagesAction() {
		$dataa= $this->getRequest()->getPost();
        if(isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '')
            {
               $fname1 = $_FILES['fileToUpload']['name'] ;
         try
           {       
	        $path = Mage::getBaseDir('media').DS."slider".DS;  //desitnation directory     
	        $fname =$profileimage; //file name
	        $uploader = new Varien_File_Uploader('fileToUpload'); //load class
	        $uploader->setAllowedExtensions(array('jpeg','jpg','png')); //Allowed extension for file
	        $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
	        $uploader->setAllowRenameFiles(true); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
	        $uploader->setFilesDispersion(false);
	        $uploader->save($path,$fname); //save the file on the specified path
	        $profileimage = $uploader->getUploadedFileName();
         
    }
    catch (Exception $e)
    {
        echo 'Error Message: '.$e->getMessage();
    }
}
         $title = $this->getRequest()->getPost('title');
         $url=  $this->getRequest()->getPost('url');
         $serials=  $this->getRequest()->getPost('serials');
         $imglabel=  $this->getRequest()->getPost('imagelabel');

	        $model = Mage::getModel('btslider/btslider')->load();
	    
	        $model->setTitle($title);
	        $model->setImages($profileimage);
	        $model->setUrl($url);
	        $model->setSerials($serials);
	        $model->setImagelabel($imglabel);
	        $model->save();
           Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('btslider')->__('Your banner image is uploaded.'));
			$this->_redirect('*/*/');
            }

  public function deletesliderimageAction()
    {
 
            $sliderid = Mage::app()->getRequest()->getPost('id');
            $delData = Mage::getModel('btslider/btslider')->load($sliderid);

            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
              try {
                 $delData->delete();

              } catch(Exception $e) {
                  echo "Image #".$delData->getId()." could not be removed: ".$e->getMessage();
              }
               Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('btslider')->__('This record is successfully deleted.'));
			  $this->_redirect('*/*/');
          }

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('btslider/btslider')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('btslider_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('btslider/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('btslider/adminhtml_btslider_edit'))
				->_addLeft($this->getLayout()->createBlock('btslider/adminhtml_btslider_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('btslider')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('btslider/btslider');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('btslider')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('btslider')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('btslider/btslider');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $btsliderIds = $this->getRequest()->getParam('btslider');
        if(!is_array($btsliderIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($btsliderIds as $btsliderId) {
                    $btslider = Mage::getModel('btslider/btslider')->load($btsliderId);
                    $btslider->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($btsliderIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $btsliderIds = $this->getRequest()->getParam('btslider');
        if(!is_array($btsliderIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($btsliderIds as $btsliderId) {
                    $btslider = Mage::getSingleton('btslider/btslider')
                        ->load($btsliderId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($btsliderIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'btslider.csv';
        $content    = $this->getLayout()->createBlock('btslider/adminhtml_btslider_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'btslider.xml';
        $content    = $this->getLayout()->createBlock('btslider/adminhtml_btslider_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}