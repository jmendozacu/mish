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

class MageWorx_DeliveryZone_Block_Adminhtml_Rates_Edit_Tab_Main
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
        return Mage::helper('catalogrule')->__('Rule Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('catalogrule')->__('Rule Information');
    }

    /**
     * Returns status flag about this tab can be showed or not
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

        $fieldset = $form->addFieldset('base_fieldset', array(
                'legend' => Mage::helper('deliveryzone')->__('General Information')
            )
        );
        if ($model->getId()) {
            $fieldset->addField('rate_id', 'hidden', array(
                'name' => 'rate_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('catalogrule')->__('Rule Name'),
            'title' => Mage::helper('catalogrule')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('catalogrule')->__('Description'),
            'title' => Mage::helper('catalogrule')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('catalogrule')->__('Status'),
            'title'     => Mage::helper('catalogrule')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('catalogrule')->__('Active'),
                '0' => Mage::helper('catalogrule')->__('Inactive'),
            ),
        ));

//        if (Mage::app()->isSingleStoreMode()) {
//            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
//            $fieldset->addField('website_ids', 'hidden', array(
//                'name'     => 'website_ids[]',
//                'value'    => $websiteId
//            ));
//            $model->setWebsiteIds($websiteId);
//        } else {
//            $field = $fieldset->addField('website_ids', 'multiselect', array(
//                'name'     => 'website_ids[]',
//                'label'     => Mage::helper('catalogrule')->__('Websites'),
//                'title'     => Mage::helper('catalogrule')->__('Websites'),
//                'required' => true,
//                'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm()
//            ));
//            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
//            $field->setRenderer($renderer);
//        }
        
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'multiselect', array(
                'name'      => 'store_ids[]',
                'label'     => $this->__('Store View'),
                'title'     => $this->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
            ));
        } else {
            $fieldset->addField('store_ids', 'hidden', array(
                'name'  => 'store_ids[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('catalogrule')->__('Customer Groups'),
            'title'     => Mage::helper('catalogrule')->__('Customer Groups'),
            'required'  => true,
            'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray()
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('catalogrule')->__('Priority'),
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
