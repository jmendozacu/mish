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

class MageWorx_Deliveryzone_Block_Adminhtml_Manager_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
	    parent::__construct();
	    $this->setId('zone_tabs');
	    $this->setDestElementId('edit_form');
	    $this->setTitle(Mage::helper('deliveryzone')->__('Zone Settings'));
	}

	protected function _beforeToHtml()
	{
	    $this->addTab('general', array(
	        'label'     => Mage::helper('deliveryzone')->__('General Settings'),
	        'title'     => Mage::helper('deliveryzone')->__('General Settings'),
	        'content'   => $this->getLayout()->createBlock('deliveryzone/adminhtml_manager_edit_tab_general')->toHtml(),
	    ));
            
            $this->addTab('restricted', array(
	        'label'     => Mage::helper('deliveryzone')->__('Countries'),
	        'title'     => Mage::helper('deliveryzone')->__('Countries'),
	        'content'   => $this->getLayout()->createBlock('deliveryzone/adminhtml_manager_edit_tab_restricted')->toHtml(),
	    ));
            $this->addTab('products', array(
                'label'     => Mage::helper('catalog')->__('Products'),
                'content'   => $this->getLayout()->createBlock('deliveryzone/adminhtml_manager_edit_tab_products')->toHtml(),
            ));
	    $this->addTab('categories', array(
	      'label'     => Mage::helper('catalog')->__('Categories'),
	      'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
	      'class'     => 'ajax',
	    ));
	    $this->addTab('shipping_method', array(
	      'label'     => Mage::helper('catalog')->__('Shipping Methods'),
	      'url'       => $this->getUrl('*/*/shipping_method', array('_current' => true)),
	      'class'     => 'ajax',
	    ));

	    return parent::_beforeToHtml();
	}
}