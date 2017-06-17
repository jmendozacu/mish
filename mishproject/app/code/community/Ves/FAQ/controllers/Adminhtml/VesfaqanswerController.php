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
class Ves_FAQ_Adminhtml_VesfaqanswerController extends Mage_Adminhtml_Controller_Action{

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
    	$answerId = $this->getRequest()->getParam('id');
    	$model  = Mage::getModel('ves_faq/answer')->load($answerId);

    	if ($model->getAnswerId() || $answerId == 0) {
    		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
    		if (!empty($data)) {
    			$model->setData($data);
    		}
    		Mage::register('answer_data', $model);
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
    		$this->_addContent($this->getLayout()->createBlock('ves_faq/adminhtml_answer_edit'))
    		->_addLeft($this->getLayout()->createBlock('ves_faq/adminhtml_answer_edit_tabs'));

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
    			Mage::helper('ves_faq')->__('Item does not exist')
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

    		$model = Mage::getModel('ves_faq/answer');

    		$model->setData($data)
    		->setId($this->getRequest()->getParam('id'));
    		try {
          if ($model->getCreatedAt == NULL || $model->getUpdateAt() == NULL) {
            $model->setCreatedAt(now())
            ->setUpdatedAt(now());
          } else {
            $model->setUpdatedAt(now());
          }
          $user = Mage::getSingleton('admin/session'); 
          $author_name = $user->getUser()->getFirstname().' '.$user->getUser()->getLastname();
          $author_email = $user->getUser()->getEmail();

          if( $model->getAuthorName() == '' ){
            $model->setAuthorName($author_name);
          }

          if( $model->getAuthorEmail() == '' ){
            $model->setAuthorEmail($author_email);
          }     

          $model->save();
          Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('ves_faq')->__('Answer was successfully saved')
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
      Mage::helper('ves_faq')->__('Unable to find answer to save')
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
    			$model = Mage::getModel('ves_faq/answer');
    			$model->setId($this->getRequest()->getParam('id'))
    			->delete();
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    				Mage::helper('adminhtml')->__('Answer was successfully deleted')
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
    	$answerIds = $this->getRequest()->getParam('answer');
    	if (!is_array($answerIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
    	} else {
    		try {
    			foreach ($answerIds as $answerId) {
    				$answer = Mage::getModel('ves_faq/answer')->load($answerId);
    				$answer->delete();
    			}
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    				Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
    					count($answerIds))
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
      	$answerIds = $this->getRequest()->getParam('answer');
      	if (!is_array($answerIds)) {
      		Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
      	} else {
      		try {
      			foreach ($answerIds as $answerId) {
      				Mage::getSingleton('ves_faq/answer')
      				->load($answerId)
      				->setStatus($this->getRequest()->getParam('status'))
      				->setIsMassupdate(true)
      				->save();
      			}
      			$this->_getSession()->addSuccess(
      				$this->__('Total of %d record(s) were successfully updated', count($answerIds))
      				);
      		} catch (Exception $e) {
      			$this->_getSession()->addError($e->getMessage());
      		}
      	}
      	$this->_redirect('*/*/index');
      }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
    	$fileName   = 'answer.csv';
    	$content    = $this->getLayout()
    	->createBlock('ves_faq/adminhtml_answer_grid')
    	->getCsv();
    	$this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
    	$fileName   = 'answer.xml';
    	$content    = $this->getLayout()
    	->createBlock('ves_faq/adminhtml_answer_grid')
    	->getXml();
    	$this->_prepareDownloadResponse($fileName, $content);
    }
  }