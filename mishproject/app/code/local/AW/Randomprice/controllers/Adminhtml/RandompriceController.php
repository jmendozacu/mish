<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Randomprice
 * @version    1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Randomprice_Adminhtml_RandompriceController extends Mage_Adminhtml_Controller_Action {
    const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';
    protected $_randompriceId = null;

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('promo/awrandomprice')
                ->_addBreadcrumb(Mage::helper('awrandomprice')->__('Randomprice Rules'), Mage::helper('adminhtml')->__('Randomprice Rules'));
        return $this;
    }

    public function indexAction() {
        return $this->_redirect('*/*/list');
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function listAction() {

        $rule = Mage::getModel('awrandomprice/randomprice');
        $collection = Mage::getModel('awrandomprice/randomprice')
                ->getCollection()
                ->getAllIds();

        $now = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        foreach ($collection as $ruleId) {

            $rule->load($ruleId);
            $rule->setStatus(AW_Randomprice_Model_Randomprice::STATUS_PENDING);

            if (strtotime($rule->getDateFrom()) < strtotime($now)) {
                $rule->setStatus(AW_Randomprice_Model_Randomprice::STATUS_STARTED);
            }

            if (strtotime($rule->getDateTo()) < strtotime($now)) {
                $rule->setStatus(AW_Randomprice_Model_Randomprice::STATUS_ENDED);
            }

            $rule->save();
        }


        $this->_initAction()
                ->_setTitle($this->__('Rules List'))
                ->_addContent(
                        $this->getLayout()->createBlock('awrandomprice/adminhtml_randomprice')
                )
                ->renderLayout();
    }

    public function massDeleteAction() {
        $randompriceIds = $this->getRequest()->getParam('randompriceid');
        if (!is_array($randompriceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($randompriceIds as $randompriceId) {
                    Mage::getModel('awrandomprice/randomprice')->load($randompriceId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($randompriceIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function massStatusAction() {
        $randompriceIds = $this->getRequest()->getParam('randompriceid');
        if (!is_array($randompriceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($randompriceIds as $randompriceId) {
                    $model = Mage::getModel('awrandomprice/randomprice')->load($randompriceId);
                    $model->setData('is_enabled', $this->getRequest()->getParam('status'));
                    $model->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully updated', count($randompriceIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('randompriceid');
        $model = Mage::getModel('awrandomprice/randomprice');
        $act = 'New Rule';
        if ($id) {
            $act = 'Edit Rule';
            $model->load($id);
            if (!$model->getRandompriceid()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awrandomprice')->__('This rule no longer exists'));
                return $this->_redirect('*/*');
            }
        }
        $this->_setTitle($this->__($act));
        $model->getConditions()->setJsFormObject('auto_conditions_fieldset');

        Mage::register('randomprice_data', $model);

        $this->loadLayout();

        $this->_setActiveMenu('promo');

        $block = $this->getLayout()->createBlock('awrandomprice/adminhtml_randomprice_edit')
                ->setData('action', $this->getUrl('*/save'));

        $this->getLayout()->getBlock('head')
                ->setCanLoadExtJs(true)
                ->setCanLoadRulesJs(true);

        $this
                ->_addContent($block)
                ->_addLeft($this->getLayout()->createBlock('awrandomprice/adminhtml_randomprice_edit_tabs'))
                ->renderLayout();
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('randompriceid')) {
            try {
                Mage::getModel('awrandomprice/randomprice')->load($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('awrandomprice')->__('Rule was successfully deleted'));
                return $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return $this->_redirect('*/*/');
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awrandomprice')->__('Delete error'));
        return $this->_redirect('*/*/');
    }

    public function saveAction() {

        $data = $this->getRequest()->getPost();

        if ($data) {
            try {
                $model = Mage::getModel('awrandomprice/randomprice');

                $data['conditions'] = $data['rule']['conditions'];
                if (isset($data['trigger'])) {
                    $triggers = $data['trigger'];
                    unset($data['trigger']);
                }
                unset($data['rule']);
                if ($data['store_ids']) {
                    if (is_array($data['store_ids'])) {
                        if (in_array('0', $data['store_ids'])) {
                            $data['store_ids'] = '0';
                        }else
                            $data['store_ids'] = implode(',', $data['store_ids']);
                    }
                }
                else {
                    $data['store_ids'] = '0';
                }

                $data = $this->_filterDateTime($data, array('date_from', 'date_to'));

                $now = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                if ($data['date_from'] == '') {
                    $data['date_from'] = $now;
                }

                //Check date

                if (strtotime($data['date_to']) > strtotime($data['date_from'])) {
                    $data['status'] = AW_Randomprice_Model_Randomprice::STATUS_STARTED;
                } else {
                    Mage::getSingleton('adminhtml/session')->addError('Ending date must be in future');
                    Mage::getSingleton('adminhtml/session')->setPageData($data);
                    $this->_redirect('*/*/edit', array('randompriceid' => $this->getRequest()->getParam('randompriceid')));
                    return;
                }

                if (strtotime($data['date_from']) > strtotime($now)) {
                    $data['status'] = AW_Randomprice_Model_Randomprice::STATUS_PENDING;
                }

                if (strtotime($data['date_to']) < strtotime($data['date_from'])) {
                    Mage::getSingleton('adminhtml/session')->addError('Ending date must be greater whan start date');
                    Mage::getSingleton('adminhtml/session')->setPageData($data);
                    $this->_redirect('*/*/edit', array('randompriceid' => $this->getRequest()->getParam('randompriceid')));
                    return;
                }

                if (!$this->getRequest()->getParam('randompriceid')) {
                    $data['status'] = AW_Randomprice_Model_Randomprice::STATUS_PENDING;
                }

                if ($this->getRequest()->getParam('randompriceid')) {
                    $model->load($this->getRequest()->getParam('randompriceid'));
                }


                if (trim($data['template']) == '') {
                    $data['template'] = '<div class="aw-randomprice"><a href="{{randomprice_link}}" rel="nofollow">{{link_title}}</a></div>';
                }


                $model->loadPost($data)->save();
                $this->_randompriceId = $model->getRandompriceid();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('awrandomprice')->__('Rule was successfully saved'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', array('randompriceid' => $model->getRandompriceid(), 'tab' => Mage::app()->getRequest()->getParam('tab')));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('randompriceid' => $this->getRequest()->getParam('randompriceid')));
                return;
            }
        }

        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction() {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
                ->setId($id)
                ->setType($type)
                ->setRule(Mage::getModel('awrandomprice/randomprice'))
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

    /**
     * Returns true when admin session contain error messages
     */
    protected function _hasErrors() {
        return (bool) count($this->_getSession()->getMessages()->getItemsByType('error'));
    }

    /**
     * Set title of page
     */
    protected function _setTitle($action) {
        if (method_exists($this, '_title')) {
            $this->_title($this->__('Randomprice Rule'))->_title($this->__($action));
        }
        return $this;
    }

}