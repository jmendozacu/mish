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


class AW_Randomprice_Block_Adminhtml_Randomprice_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('randompriceid');
        $this->setDefaultSort('randompriceid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('awrandomprice/randomprice')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $helper = Mage::helper('awrandomprice');

        $this->addColumn('randompriceid', array(
            'header' => $helper->__('ID'),
            'align' => 'right',
            'width' => '5',
            'index' => 'randompriceid'
        ));

        $this->addColumn('name', array(
            'header' => $helper->__('Block title'),
            'align' => 'left',
            'index' => 'name'
        ));

        $this->addColumn('is_enabled', array(
            'header' => $helper->__('Status'),
            'align' => 'center',
            'width' => '80px',
            'index' => 'is_enabled',
            'type' => 'options',
            'options' => array(
                AW_Randomprice_Model_Source_Status::ENABLED => $helper->__(AW_Randomprice_Model_Source_Status::ENABLED_LABEL),
                AW_Randomprice_Model_Source_Status::DISABLED => $helper->__(AW_Randomprice_Model_Source_Status::DISABLED_LABEL)
            )
        ));
        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        $this->addColumn('date_from', array(
            'header' => $helper->__('Active from'),
            'index' => 'date_from',
            'type' => 'date',
            'format' => $outputFormat,
            'time' => true
        ));

        $this->addColumn('date_to', array(
            'header' => $helper->__('Active to'),
            'index' => 'date_to',
            'type' => 'date',
            'format' => $outputFormat,
            'time' => true
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Running Status'),
            'align' => 'center',
            'width' => '160',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('awrandomprice/source_running')->toOptionArray(),
        ));

        $this->addColumn('autom_display', array(
            'header' => $helper->__('Automation'),
            'align' => 'center',
            'width' => '160',
            'index' => 'autom_display',
            'type' => 'options',
            'options' => Mage::getSingleton('awrandomprice/source_automation')->getOptionArray(),
        ));


        parent::_prepareColumns();
        return $this;
    }

    protected function _prepareMassaction() {

        $this->setMassactionIdField('randompriceid');
        $this->getMassactionBlock()->setFormFieldName('randompriceid');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('awrandomprice')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('awrandomprice')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('awrandomprice/source_status')->toOptionArray();

        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('awrandomprice')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('awrandomprice')->__('Status'),
                    'values' => $statuses
                )
            ),
            'confirm' => Mage::helper('awrandomprice')->__('Are you sure?'),
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit/', array('randompriceid' => $row->getRandompriceid()));
    }

}
