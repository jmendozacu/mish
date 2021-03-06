<?php
/**
 * Geoip Ultimate Lock extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Geoipultimatelock
 * @author     R.Rao <rafay.tahir@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
class FME_Geoipultimatelock_Block_Adminhtml_Geoipultimatelock_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{ 
    /**
   * Create Conditions form
   * @return Conditions form
   */
    protected function _prepareForm()
    {
	  $model = Mage::getModel('geoipultimatelock/geoipultimatelock')->load((int) $this->getRequest()->getParam('id'))
                  ->humanizeData();
	
	  $form = new Varien_Data_Form();
	  $helper = Mage::helper('geoipultimatelock'); 
  
	  $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
		  ->setTemplate('promo/fieldset.phtml')
		  ->setNewChildUrl($this->getUrl('*/*/newConditionHtml', array(
		      'form' => 'css_conditions_fieldset',
		      'prefix' => 'css', 
		      'rule' => base64_encode('geoipultimatelock/geoipultimatelock_product_rulecss'))));
  
	  $fieldset = $form->addFieldset('css_conditions_fieldset', array(
		      'legend' => $this->__('Conditions (leave blank for all products)')
		  ))->setRenderer($renderer);
   
	  $rule = Mage::getModel('geoipultimatelock/geoipultimatelock_product_rulecss');
	  $rule->getConditions()->setJsFormObject('css_conditions_fieldset');
	  $rule->getConditions()->setId('css_conditions_fieldset');
  
	  $rule->setForm($fieldset);
	  if ($model->getData('rules') && is_array($model->getData('rules')->getData('conditions'))) {
	      $conditions = $model->getData('rules')->getData('conditions');
	      $rule->getConditions()->loadArray($conditions, 'css');
	      $rule->getConditions()->setJsFormObject('css_conditions_fieldset');
	  }
  
	  $fieldset->addField('css_conditions', 'text', array(
	      'name' => 'css_conditions',
	      'label' => $this->__('Apply To'),
	      'title' => $this->__('Apply To'),
	      'required' => true,
	  ))->setRule($rule)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
  
	  $form->setValues($model->getData());
	  $this->setForm($form);
  
	  return parent::_prepareForm();
    }
}
