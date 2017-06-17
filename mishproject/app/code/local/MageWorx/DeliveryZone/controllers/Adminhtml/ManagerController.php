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

class Mageworx_Deliveryzone_Adminhtml_ManagerController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('sales/deliveryzone')
                ->_addBreadcrumb(Mage::helper('deliveryzone')->__('Shipping Suite'), Mage::helper('deliveryzone')->__('Manager'));

        return $this;
    }

    protected function _initZone()
    {
    $id = $this->getRequest()->getParam('id');
            $zone = Mage::getModel('deliveryzone/zone')->load($id);
            Mage::register('current_zone', $zone);

            return $zone;
    }

    protected function _setTitle()
    {
        return $this->_title($this->__('Shipping Suite'))->_title($this->__('Manage Zones'));
    }
    
    public function categoriesAction()
    {
        $zone = $this->_initZone();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('deliveryzone/adminhtml_manager_edit_tab_categories')->toHtml()
        );
    }
    public function shipping_methodAction()
    {
        $zone = $this->_initZone();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('deliveryzone/adminhtml_manager_edit_tab_shippingmethod')->toHtml()
        );
    }

    public function categoriesJsonAction()
    {
        $zone = $this->_initZone();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('deliveryzone/adminhtml_manager_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
    public function shipping_methodJsonAction()
    {
        $zone = $this->_initZone();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('deliveryzone/adminhtml_manager_edit_tab_shippingmethod')
                ->getCategoryChildrenJson($this->getRequest()->getParam('shipping_method'))
        );
    }

	public function indexAction()
	{
            $this->_setTitle();
            
	    $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('deliveryzone/adminhtml_manager'))
            ->renderLayout();
	}
        private function _loadSelectedProducts() {
            $products = array();
            $collection = Mage::getResourceModel('deliveryzone/zone_product_collection')->loadByZoneId(Mage::app()->getRequest()->getParam('id',0));
            foreach ($collection as $item) {
                $products[] = $item->getProductId();
            }
            Mage::register('selected_products', $products);
            //$this->getRequest()->setParam('selected_products',$products);
        }
	public function editAction()
	{
            $this->_setTitle();
            $id     = $this->getRequest()->getParam('id');
            $model  = Mage::getModel('deliveryzone/zone')->load($id);
            $this->_title($model->getZoneId() ? $model->getTitle() : $this->__('New Zone'));
                
            if ($model->getId() || $id == 0) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if (!empty($data)) {
                    $model->setData($data);
                }
                Mage::register('zone_data', $model);
                $this->_loadSelectedProducts();
                $this->loadLayout();
                $this->_setActiveMenu('sales/deliveryzone');

                $this->_addBreadcrumb(Mage::helper('deliveryzone')->__('Shipping Suite'), Mage::helper('deliveryzone')->__('Shipping Suite'));
                $this->_addBreadcrumb(Mage::helper('deliveryzone')->__('Manage Zone'), Mage::helper('deliveryzone')->__('Manage Zone'));

                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

                $this->_addContent($this->getLayout()->createBlock('deliveryzone/adminhtml_manager_edit'))
                        ->_addLeft($this->getLayout()->createBlock('deliveryzone/adminhtml_manager_edit_tabs'));

                $this->renderLayout();
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('deliveryzone')->__('Item does not exist'));
                $this->_redirect('*/*/');
            }
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	public function saveAction()
	{
            if ($data = $this->getRequest()->getPost()) {
              //  echo '<pre>'; print_r($data); exit;
                $model = Mage::getModel('deliveryzone/zone');
                $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

                try {
                    $model->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('deliveryzone')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('deliveryzone')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}

	public function deleteAction()
	{
            if ($this->getRequest()->getParam('id') > 0) {
                try {
                    $model = Mage::getModel('deliveryzone/zone')->load($this->getRequest()->getParam('id'));
                    $model->delete();

                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('deliveryzone')->__('Item was successfully deleted'));
                    $this->_redirect('*/*/');
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }
            }
            $this->_redirect('*/*/');
	}

    public function massStatusAction() {
        // echo "<pre>"; print_r($this->getRequest()->getParams()); exit;
        Mage::register("deliveryzone_massaction_status",TRUE,TRUE);
        $zoneIds = $this->getRequest()->getParam('zones');
        $staus = $this->getRequest()->getParam('status');
        foreach ($zoneIds as $zoneId) {
            $model = Mage::getModel('deliveryzone/zone')->load($zoneId);
            $model->setStatus($staus)->save();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('deliveryzone')->__('Status has been changed.')
                );
        return $this->_redirect('*/*/');
    }
    
    public function massDeleteAction()
    {
        $zoneIds = $this->getRequest()->getParam('zones');
        if(!is_array($zoneIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('deliveryzone')->__('Please select item(s)'));
        } 
        else {
            try {
                foreach ($zoneIds as $zoneId) {
                    $zone = Mage::getModel('deliveryzone/zone')->load($zoneId);
                    $zone->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('deliveryzone')->__('Total of %d record(s) were successfully deleted', count($zoneIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function ajaxGridAction() {
        $this->_loadSelectedProducts();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('deliveryzone/adminhtml_manager_edit_tab_products', 'category.product.grid')
                ->toHtml()
        );
    }
}