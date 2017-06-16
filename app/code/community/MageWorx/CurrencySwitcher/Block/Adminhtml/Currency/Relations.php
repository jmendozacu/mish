<?php
/**
 * MageWorx
 * Currency Switcher Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_CurrencySwitcher_Block_Adminhtml_Currency_Relations extends Mage_Adminhtml_Block_Template
{
    /**
     * Custom currency relation data
     *
     * @var array
     */
    protected $_relationsData = array();

    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'mageworx_currencyswitcher';
        $this->_controller = 'adminhtml_relations';
        parent::__construct();
    }

    /**
     * Returns page header text
     *
     * @return string
     */
    public function getHeader()
    {
        return Mage::helper('mageworx_currencyswitcher')->__('Manage Currency Relations');
    }

    /**
     * Returns 'Save Currency Symbol' button's HTML code
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $this->getLayout()->createBlock('adminhtml/widget_button');
        $block->setData(array(
            'label'     => Mage::helper('mageworx_currencyswitcher')->__('Save Currency Relations'),
            'onclick'   => 'currencyRelationsForm.submit();',
            'class'     => 'save'
        ));

        return $block->toHtml();
    }

    /**
     * Returns 'Refresh' button's HTML code
     *
     * @return string
     */
    public function getRefreshButtonHtml()
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $this->getLayout()->createBlock('adminhtml/widget_button');
        $block->setData(array(
            'label'     => Mage::helper('mageworx_currencyswitcher')->__('Refresh'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/refresh') . '\')',
            'class'     => 'save'
        ));

        return $block->toHtml();
    }

    /**
     * Returns URL for save action
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Returns Custom currency relation data
     *
     * @return array
     */
    public function getCurrencyRelations()
    {
        if (!$this->_relationsData) {
            $this->_relationsData = Mage::getModel('mageworx_currencyswitcher/relations')->getCollection()->getItems();
        }
        return $this->_relationsData;
    }

    /**
     * Returns inheritance text
     *
     * @return string
     */
    public function getInheritText()
    {
        return Mage::helper('mageworx_currencyswitcher')->__('Use Standard');
    }
}
