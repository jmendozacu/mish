<?php
/**
 * MageWorx
 * Currency Switcher Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_CurrencySwitcher_Adminhtml_Mageworx_Currencyswitcher_RelationsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/currency')
            ->_addBreadcrumb(
                Mage::helper('mageworx_currencyswitcher')->__('System'),
                Mage::helper('mageworx_currencyswitcher')->__('System')
            )
            ->_addBreadcrumb(
                Mage::helper('mageworx_currencyswitcher')->__('Manage Currency Relations'),
                Mage::helper('mageworx_currencyswitcher')->__('Manage Currency Relations')
            );

        $this->_title($this->__('System'))
            ->_title($this->__('Manage Currency Relations'));

        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $post = $this->getRequest()->getPost();

        try {
            if (isset($post['currency_relation'])) {
                foreach ($post['currency_relation'] as $id => $relation) {

                    if (empty($relation['countries'])) {
                        continue;
                    }

                    if (isset($relation['countries']['use_default'])) {
                        $countries = Mage::helper('mageworx_currencyswitcher')->getCountryByCurrency($relation['code']);
                    } else {
                        $countries = $relation['countries'];
                    }
                    if (is_array($countries)) {
                        $countries = implode(',', $countries);
                    }

                    $data = array(
                        'relation_id'   => $id,
                        'currency_code' => $relation['code'],
                        'countries'     => $countries
                    );

                    $relationModel = Mage::getModel('mageworx_currencyswitcher/relations')->load($id);
                    $relationModel->setData($data);
                    $relationModel->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('mageworx_currencyswitcher')->__('Currency relations were saved successfully.')
                );
            } else {
                throw new Exception($this->__('No data to save'));
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirectReferer();
    }

    /**
     * Refresh available currencies
     */
    public function refreshAction()
    {
        try {
            $currentCodes   = array();
            $relationModel  = Mage::getModel('mageworx_currencyswitcher/relations');
            $collection     = $relationModel->getCollection();

            $availableCodes = array();

            foreach(Mage::app()->getStores() as $store){
                foreach($store->getAvailableCurrencyCodes(true) as $code){
                    if(!in_array($code, $availableCodes)){
                        $availableCodes[] = $code;
                    }
                }
            }

            foreach ($collection->getItems() as $item) {
                if (!in_array($item->getCurrencyCode(), $availableCodes)) {
                    $item->delete();
                    continue;
                }
                $currentCodes[] = $item->getCurrencyCode();
            }

            foreach ($availableCodes as $code) {
                if (!in_array($code, $currentCodes)) {
                    $data = array(
                        'currency_code' => $code,
                        'countries' => Mage::helper('mageworx_currencyswitcher')->getCountryByCurrency($code)
                    );
                    $relationModel->setData($data)->save();
                }
            }


            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('mageworx_currencyswitcher')->__('Currency relations were saved successfully.')
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/currency/relations');
    }
}