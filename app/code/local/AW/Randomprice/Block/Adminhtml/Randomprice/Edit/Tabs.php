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


class AW_Randomprice_Block_Adminhtml_Randomprice_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('randomprice_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('awrandomprice')->__('Randomprice Rule'));
    }

    protected function _beforeToHtml() {

        $helper = Mage::helper('awrandomprice');
        $tab = $this->getRequest()->getParam('tab');
        $layout = $this->getLayout();

        $this->addTab('general', array(
            'label' => $helper->__('General'),
            'title' => $helper->__('General'),
            'content' => $layout->createBlock('awrandomprice/adminhtml_randomprice_edit_tab_general')->toHtml(),
            'active' => ( $tab == 'randomprice_tabs_general') ? true : false,
        ));


        $this->addTab('automation', array(
            'label' => $helper->__('Automation'),
            'title' => $helper->__('Automation'),
            'content' => $layout->createBlock('awrandomprice/adminhtml_randomprice_edit_tab_automation')->toHtml(),
            'active' => ($tab == 'randomprice_tabs_automation') ? true : false,
        ));


        $this->addTab('design', array(
            'label' => $helper->__('Design'),
            'title' => $helper->__('Design'),
            'content' => $layout->createBlock('awrandomprice/adminhtml_randomprice_edit_tab_design')->toHtml(),
            'active' => ($tab == 'randomprice_tabs_design') ? true : false,
        ));

        return parent::_beforeToHtml();
    }

}