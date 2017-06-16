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
class Ves_FAQ_Adminhtml_VesfaqcategoryController extends Mage_Adminhtml_Controller_Action{

    /**
     * index action
     */
  public function indexAction(){
      $this->loadLayout();
      $this->renderLayout();
  }

  public function newAction()
  {
      $this->_forward('edit');
  }

    /**
     * view and edit item action
     */
    public function editAction()
    {
    	$categoryId = $this->getRequest()->getParam('id');
    	$model  = Mage::getModel('ves_faq/category')->load($categoryId);

    	if ($model->getCategoryId() || $categoryId == 0) {
    		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
    		if (!empty($data)) {
    			$model->setData($data);
    		}
    		Mage::register('category_data', $model);
    		$this->loadLayout();
    		$this->_setActiveMenu('venusextension/ves_faq');

    		$this->_addBreadcrumb(
    			Mage::helper('adminhtml')->__('Item Manager'),
    			Mage::helper('adminhtml')->__('Item Manager')
    			);
    		$this->_addBreadcrumb(
    			Mage::helper('adminhtml')->__('Item News'),
    			Mage::helper('adminhtml')->__('Item News')
    			);
    		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
    		$this->_addContent($this->getLayout()->createBlock('ves_faq/adminhtml_category_edit'))
    		->_addLeft($this->getLayout()->createBlock('ves_faq/adminhtml_category_edit_tabs'));

            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {

                $this->getLayout()->getBlock('head')
                ->setCanLoadTinyMce(true)
                ->addItem('js','tiny_mce/tiny_mce.js')
                ->addItem('js','mage/adminhtml/wysiwyg/tiny_mce/setup.js')
                ->addJs('mage/adminhtml/browser.js')
                ->addJs('prototype/window.js')
                ->addJs('lib/FABridge.js')
                ->addJs('lib/flex.js')
                ->addJs('mage/adminhtml/flexuploader.js')
                ->addItem('js_css','prototype/windows/themes/default.css')
                ->addCss('lib/prototype/windows/themes/magento.css');
            }

            $this->renderLayout();
        }else{
          Mage::getSingleton('adminhtml/session')->addError(
           Mage::helper('ves_faq')->__('Category does not exist')
           );
          $this->_redirect('*/*/');
      }
  }

    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {



            if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('image');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS . 'ves_faq';
                    $result = $uploader->save($path, $_FILES['image']['name'] );
                    $data['image'] = $result['file'];
                } catch (Exception $e) {
                    $data['image'] = $_FILES['image']['name'];
                }
            }elseif (isset($data['image']['delete'])) {
                $image = $data['image']['value'];
                $image = explode('/',$image);
                if(!$image){
                    return;
                }
                $category_image_path = Mage::getBaseDir('media'). DS . 'ves_faq' . DS . $image[1];
                if(!file_exists($category_image_path)){
                    return ;
                }
                try {
                    unlink($category_image_path);
                    $data['image'] = '';
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            }else{
                unset($data['image']);
            }

            $model = Mage::getModel('ves_faq/category');
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
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ves_faq')->__('Category was successfully saved')
                    );
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
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('ves_faq')->__('Unable to find category to save')
            );
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('ves_faq/category');
                $model->setId($this->getRequest()->getParam('id'))
                ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Category was successfully deleted')
                    );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }


    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $categoryId = $this->getRequest()->getParam('category');
        if (!is_array($categoryId)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($categoryId as $categoryId) {
                    $category = Mage::getModel('ves_faq/category')->load($categoryId);
                    $category->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                        count($categoryId))
                    );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

      /**
     * mass change status for item(s) action
     */
      public function massStatusAction()
      {
        $categoryId = $this->getRequest()->getParam('category');
        if (!is_array($categoryId)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($categoryId as $categoryId) {
                    Mage::getSingleton('ves_faq/category')
                    ->load($categoryId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($categoryId))
                    );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'ves_faq_category.xml';
        $content    = $this->getLayout()
        ->createBlock('ves_faq/adminhtml_category_exportGrid')
        ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'ves_faq_category.csv';
        $content    = $this->getLayout()
        ->createBlock('ves_faq/adminhtml_category_exportGrid')
        ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function uploadCsvAction() {
      $this->loadLayout();
      $block = $this->getLayout()->createBlock('ves_faq/adminhtml_category_upload');
      $this->getLayout()->getBlock('content')->append($block);
      $this->renderLayout();
  }

  public function importCsvAction(){
      $profile = $this->getRequest()->getParam('file');
      $sub_folder = $this->getRequest()->getParam('subfolder');

      $filepath = Mage::helper("ves_faq")->getUploadedFile();

      if ($filepath != null) {
        try {
          $stores = Mage::helper("ves_faq")->getAllStores();
          // import into model
          Mage::getSingleton('ves_faq/import_category')->process($filepath, $stores);
          Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('CSV Imported Successfully'));
          $this->_redirect('*/*/index');

      } catch (Exception $e) {
          Mage::logException($e);
          Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('An Error occured importing CSV.'));
          Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } // end if
        }else{
            $this->_redirect('*/*/index');
        }
    }

     /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('vesextensions/faq/category');
    }
}