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

class MageWorx_Deliveryzone_Block_Adminhtml_Manager_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('general', array('legend' => Mage::helper('deliveryzone')->__('General Settings')));

        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('deliveryzone')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
        ));
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('deliveryzone')->__('Status'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'status',
            'options'   => array(
                    1 => Mage::helper('deliveryzone')->__('Active'),
                    0 => Mage::helper('deliveryzone')->__('Inactive')
                ),
        ));

        if (Mage::getSingleton('adminhtml/session')->getZoneData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getZoneData());
            Mage::getSingleton('adminhtml/session')->setZoneData(null);
        } elseif (Mage::registry('zone_data')) {
            $form->setValues(Mage::registry('zone_data')->getData());
        }
        return parent::_prepareForm();
    }
}