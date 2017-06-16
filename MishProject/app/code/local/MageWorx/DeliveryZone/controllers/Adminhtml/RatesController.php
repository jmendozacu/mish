<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx DeliveryZone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class Mageworx_Deliveryzone_Adminhtml_RatesController extends Mage_Adminhtml_Controller_Action
{
    
   const   FIELD_NAME_SOURCE_FILE = 'import_file';
   
   protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/deliveryzone')
            ->_addBreadcrumb(
                Mage::helper('deliveryzone')->__('Shipping Suite'),
                Mage::helper('deliveryzone')->__('Shipping Suite')
            );
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Shipping Suite'))->_title($this->__('Rates'));

        $this->_initAction()
            ->_addBreadcrumb(
                Mage::helper('deliveryzone')->__('Rates'),
                Mage::helper('deliveryzone')->__('Rates')
            )
            ->renderLayout();
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Shipping Suite Rate'));
        $this->loadLayout();
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('deliveryzone/rates');

        if ($id) {
            $model->load($id);
            if (! $model->getRateId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('deliveryzone')->__('This rule no longer exists.')
                );
                $this->_redirect('*/*');
                return;
            }
        }

        $this->_title($model->getRateId() ? $model->getName() : $this->__('New Rule'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setJsFormObject('rate_conditions_fieldset');

        Mage::register('current_deliveryzone_rate', $model);

        $this->_initAction()->getLayout()->getBlock('deliveryzone_rate_edit')
             ->setData('action', $this->getUrl('*/adminhtml_rates/save'));

        $breadcrumb = $id
            ? Mage::helper('catalogrule')->__('Edit Rule')
            : Mage::helper('catalogrule')->__('New Rule');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb)->renderLayout();
    }
    
     public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('catalogrule/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
    
    public function duplicateAction() {
        $parentId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('deliveryzone/rates')->load($parentId);
        //echo "<pre>"; print_r($model->getData()); exit;
        $model->setId(NULL)
                ->setIsActive(false)
                ->setName($this->__('Duplicate')." ".$model->getName())
                ->save();
        $this->_redirect('*/*/');
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                
                $model = Mage::getModel('deliveryzone/rates');
                $data = $this->getRequest()->getPost();
                $redirectBack   = $this->getRequest()->getParam('back', false);
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                if ($id = $this->getRequest()->getParam('rate_id')) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('catalogrule')->__('Wrong rule specified.'));
                    }
                }

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
                
                Mage::dispatchEvent(
                    'deliveryzone_controller_rate_prepare_save',
                    array('data' => $data,'object'=>$this)
                );
                $model->loadPost($data);
                
                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
                
                $model->save();
                Mage::dispatchEvent(
                    'deliveryzone_controller_rate_save_after',
                    array('object' => $model)
                );
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('deliveryzone')->__('The rule has been saved.')
                );
                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id'    => $model->getId(),
                        '_current'=>true
                    ));
                } else {
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('deliveryzone')->__('An error occurred while saving the rule data. Please review the log and try again.')
                );
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rate_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $zoneIds = $this->getRequest()->getParam('zones');
        $staus = $this->getRequest()->getParam('status');
        foreach ($zoneIds as $zoneId) {
            $model = Mage::getModel('deliveryzone/rates')->load($zoneId);
            $model->delete();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('deliveryzone')->__('Rules has been deleted.')
                );
        return $this->_redirect('*/*/');
    }
    
    public function massStatusAction() {
        $zoneIds = $this->getRequest()->getParam('zones');
        $staus = $this->getRequest()->getParam('status');
        foreach ($zoneIds as $zoneId) {
            $model = Mage::getModel('deliveryzone/rates')->load($zoneId);
            $model->setIsActive($staus)->save();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('deliveryzone')->__('Status has been changed.')
                );
        return $this->_redirect('*/*/');
    }
  
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('deliveryzone/rates');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('deliveryzone')->__('The rate has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('deliveryzone')->__('An error occurred while deleting the rate. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('deliveryzone')->__('Unable to find a rate to delete.')
        );
        $this->_redirect('*/*/');
    }
    
    public function exportRatesAction() {
        $data = Mage::getModel('deliveryzone/rates_exportImport')->export();
        $this->_prepareDownloadResponse($data[0], array(
            'type'  => 'filename',
            'value' => $data[1]
        ));
    }
    
    public function emptyExportFileAction() {
        $data = Mage::getModel('deliveryzone/rates_exportImport')->generateEmptyFile();
        $this->_prepareDownloadResponse($data[0], array(
            'type'  => 'filename',
            'value' => $data[1]
        ));
    }
    public function importAction() {
        $this->_title($this->__('Shipping Suite'))->_title($this->__('Import Rates'));

        $this->_initAction()
            ->_addBreadcrumb(
                Mage::helper('deliveryzone')->__('Import Rates'),
                Mage::helper('deliveryzone')->__('Import Rates')
            )
            ->renderLayout();
    }
    
    public function importRatesSaveAction() {
        
        if(!empty($_FILES[self::FIELD_NAME_SOURCE_FILE]['name'])) {
            Mage::getModel('deliveryzone/rates_exportImport')->import();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('deliveryzone')->__('Import was successfull')
        );
        $this->_redirect('*/*/');
        return;
    }
}