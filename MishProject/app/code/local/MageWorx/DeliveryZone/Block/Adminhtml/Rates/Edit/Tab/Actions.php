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

class MageWorx_DeliveryZone_Block_Adminhtml_Rates_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('deliveryzone')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('deliveryzone')->__('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_deliveryzone_rate');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rate_');

        $fieldset = $form->addFieldset('action_fieldset', array(
                'legend' => Mage::helper('deliveryzone')->__('Update Prices Using the Following Information')
            )
        );

        $fieldset->addField('simple_action', 'select', array(
            'label'     => Mage::helper('deliveryzone')->__('Apply'),
            'name'      => 'simple_action',
            'options'   => Mage::getModel('deliveryzone/rates_action_product')->getOperatorOptions(),
        ));

        $fieldset->addField('shipping_cost', 'text', array(
            'name'      => 'shipping_cost',
         //   'required'  => true,
            'class'     => 'validate-not-negative-number',
            'label'     => Mage::helper('deliveryzone')->__('Shipping Cost'),
//            'note'      => $this->__('Original product cart price, without discounts.')
        ));
        $fieldset->addField('surcharge_fixed', 'text', array(
            'name'      => 'surcharge_fixed',
            'label'     => Mage::helper('deliveryzone')->__('Surcharge (Fixed)'),
        ));
        $fieldset->addField('surcharge_percent', 'text', array(
            'name'      => 'surcharge_percent',
            'label'     => Mage::helper('deliveryzone')->__('Surcharge (Percentage)'),
        ));
        $fieldset->addField('fixed_per_product', 'text', array(
            'name'      => 'fixed_per_product',
            'label'     => Mage::helper('deliveryzone')->__('Fixed per Product'),
        ));
        $fieldset->addField('percent_per_product', 'text', array(
            'name'      => 'percent_per_product',
            'label'     => Mage::helper('deliveryzone')->__('Percentage per Product'),
        ));
        $fieldset->addField('percent_per_item', 'text', array(
            'name'      => 'percent_per_item',
            'label'     => Mage::helper('deliveryzone')->__('Percentage per Item'),
        ));
        $fieldset->addField('fixed_per_item', 'text', array(
            'name'      => 'fixed_per_item',
            'label'     => Mage::helper('deliveryzone')->__('Fixed per Item'),
        ));
        $fieldset->addField('percent_per_order', 'text', array(
            'name'      => 'percent_per_order',
            'label'     => Mage::helper('deliveryzone')->__('Percentage per Order'),
        ));
        $fieldset->addField('fixed_per_weight', 'text', array(
            'name'      => 'fixed_per_weight',
            'label'     => Mage::helper('deliveryzone')->__('Fixed per 1 unit of weight'),
        ));

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
