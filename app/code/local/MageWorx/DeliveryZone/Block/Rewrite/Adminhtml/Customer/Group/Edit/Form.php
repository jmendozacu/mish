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

class MageWorx_DeliveryZone_Block_Rewrite_Adminhtml_Customer_Group_Edit_Form extends Mage_Adminhtml_Block_Customer_Group_Edit_Form
{
    /**
     * Prepare form for render
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if(!Mage::getStoreConfigFlag('mageworx_deliveryzone/deliveryzone/customergroup_carrier'))
        {
            return $this;
        }
        $form = $this->getForm();
        $form->setMethod("POST");
        
        $main_fieldset = $form->addFieldset('mw_carriers', array('legend'=>Mage::helper('deliveryzone')->__('Shipping Methods')));
        $shipping     = Mage::getSingleton('shipping/config');
        $configFields = Mage::getSingleton('adminhtml/config');
        $section      = 'carriers';
        $sections     = $configFields->getSections($section);
        $section      = $sections->$section->groups;
        $carriers     = $shipping->getAllCarriers();
        $this->_loadSavedCarriers();
        ksort($carriers);
        $optionsActive = array(
            0 => Mage::helper('deliveryzone')->__('No'),
            1=>Mage::helper('deliveryzone')->__('Yes')
        );
        foreach ($carriers as $_code => $carrier) {
            if (!$section->{$_code}) continue;
            $title = Mage::getStoreConfig("carriers/$_code/title");
            $fieldset = $main_fieldset->addFieldset('carrier_'.$_code, array('legend' => Mage::helper('deliveryzone')->__('%s Settings', $title)));

            $fieldset->addField($_code.'_active', 'select', array(
                'label'     => Mage::helper('deliveryzone')->__('Allowed'),
                'title'     => Mage::helper('deliveryzone')->__('Shipping Method Allowed'),
                'name'      => 'active['.$_code.']',
                'required'  => true,
                'options'   => $optionsActive,
                'value'     => ($this->getData('exist_carriers_data'))?$this->getData($_code.'_active'):Mage::getStoreConfig("carriers/$_code/active")
            ));
            
            $options = array();
            if($_methods = $carrier->getAllowedMethods())
            {
                foreach($_methods as $_mcode => $_method)
                {
                    $options[] = array('value' => $_code . '_' . $_mcode, 'label' => $_method);
                }
            }
            $allowedMethods = array();
            //echo $_code . Mage::getStoreConfig("carriers/$_code/active");
            if(!$this->getData('exist_carriers_data') && Mage::getStoreConfig("carriers/$_code/active")) {
                if($_methods = $carrier->getAllowedMethods())
                {
                    foreach($_methods as $_mcode => $_method)
                    {
                        $allowedMethods[] = $_code . '_' . $_mcode;
                    }
                }
               
                if(sizeof($_methods)<2) {
                    foreach($_methods as $_mcode => $_method)
                    {
                        $allowedMethods[] = $_code . '_' . $_mcode;
                    }
                }
            }

            $fieldset->addField($_code.'_methods', 'multiselect', array(
                'label'  => Mage::helper('deliveryzone')->__('Allowed Methods'),
                'name'   => 'methods['.$_code.'][]',
                'values' => $options,
                'value'  => ($this->getData('exist_carriers_data'))?explode(',', $this->getData($_code.'_methods')):$allowedMethods
            ));
            
        }
        
        $this->setForm($form);
    }
    
    private function _loadSavedCarriers() {
        $collection = Mage::getResourceModel('deliveryzone/customer_group_collection')->loadByGroupId(Mage::app()->getRequest()->getParam('id',0));
        if($collection->getSize()>0) {
            $this->setData('exist_carriers_data',true);
            foreach ($collection as $item) {
                $this->setData($item->getCarrierId().'_active',true);
                $this->setData($item->getCarrierId().'_methods',$item->getAllowedMethods());
            }
        }
        return true;
    }
}
