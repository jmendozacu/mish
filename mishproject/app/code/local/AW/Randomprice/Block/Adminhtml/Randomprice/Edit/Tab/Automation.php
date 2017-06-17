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


class AW_Randomprice_Block_Adminhtml_Randomprice_Edit_Tab_Automation extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $model = Mage::registry('randomprice_data');
        /* if($model){
          $model->_conditions->setJsFormObject('awrandomprice_conditions_fieldset');
          } */
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('auto_');
        $helper = Mage::helper('awrandomprice');




        $automation_fieldset = $form->addFieldset('design_design', array(
            'legend' => $this->__('Automation')
                ));
        $automation_fieldset->addField('autom_display', 'select', array(
            'name' => 'autom_display',
            'label' => $this->__('Display automatically'),
            'title' => $this->__('Display automatically'),
            'required' => true,
            'values' => Mage::getModel('awrandomprice/source_automation')->getOptionArray()
        ));



        /* price fildset */
        $price_fieldset = $form->addFieldset('price_change', array(
            'legend' => $this->__('Price Change')
                ));
        
        $price_fieldset->addField('price_increase', 'text', array(
            'name' => 'price_increase',
            'label' => $this->__('Price increase, %'),
            'title' => $this->__('Price increase, %'),
        ));
        
        $price_fieldset->addField('price_decrease', 'text', array(
            'name' => 'price_decrease',
            'label' => $this->__('Price decrease, %'),
            'title' => $this->__('Price decrease, %'),
        ));
        
        $price_fieldset->addField('allow_special_price', 'select', array(
            'name' => 'allow_special_price',
            'label' => $this->__('Apply to product with Special Price'),
            'title' => $this->__('Apply to product with Special Price'),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));


        /* conditions */
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('*/adminhtml_randomprice/newConditionHtml/form/auto_conditions_fieldset'));

        $conditions_fieldset = $form->addFieldset('conditions_fieldset', array(
                    'legend' => $this->__('Conditions')
                ))->setRenderer($renderer);




        $conditions_fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => $this->__('Conditions'),
            'title' => $this->__('Conditions'),
            'required' => false,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}