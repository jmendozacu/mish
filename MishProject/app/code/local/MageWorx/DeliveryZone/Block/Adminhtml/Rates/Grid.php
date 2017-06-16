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

class MageWorx_DeliveryZone_Block_Adminhtml_Rates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    /**
     * Initialize grid
     * Set sort settings
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setId('deliveryzone_rates_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    private function _getStatusses() {
        return array(
                1 => Mage::helper('deliveryzone')->__('Active'),
                0 => Mage::helper('deliveryzone')->__('Inactive')
            );
    }

    /**
     * Add stores to catalog rules collection
     * Set collection
     *
     * @return MageWorx_DeliveryZone_Block_Adminhtml_Rates_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection CatalogRule_Model_Mysql4_Rule_Collection */
        $collection = Mage::getModel('deliveryzone/rates')->getResourceCollection();
        $collection->addStoresToResult(true);
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Add grid columns
     *
     * @return MageWorx_DeliveryZone_Block_Adminhtml_Rates_Grid
     */
     protected function _prepareColumns()
    {
        $this->addColumn('rate_id', array(
            'header'    => Mage::helper('catalogrule')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rate_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('catalogrule')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('catalogrule')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => $this->_getStatusses(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $stores = Mage::getSingleton('adminhtml/system_store')->getStoreOptionHash();
            $stores[0] = $this->__('All Store Views');
            ksort($stores);
            $this->addColumn('rule_store', array(
                'header'    => Mage::helper('catalogrule')->__('Store View'),
                'align'     =>'left',
                'index'     => 'store_ids',
                'type'      => 'options',
                'sortable'  => false,
                'options'   => $stores,
                'width'     => 200,
                'renderer'  => 'MageWorx_DeliveryZone_Block_Adminhtml_Rates_Grid_Stores',
            ));
        }
        
        $this->addColumn('action',
		    array(
		        'header'    => Mage::helper('deliveryzone')->__('Action'),
		        'width'     => 100,
		        'type'      => 'action',
		        'getter'    => 'getId',
		        'actions'   => array(
		            array(
		                'caption' => Mage::helper('deliveryzone')->__('Edit'),
		                'url'     => array('base'=> '*/*/edit'),
		                'field'   => 'id'
		            )
		        ),
		        'filter'    => false,
		        'sortable'  => false,
		        'is_system' => true,
		));

        parent::_prepareColumns();
        return $this;
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('zone_id');
        $this->getMassactionBlock()->setFormFieldName('zones');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('deliveryzone')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('deliveryzone')->__('Are you sure?')
        ));
        $statuses = $this->_getStatusses();
       // array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('changestatus', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('deliveryzone')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
       
        return $this;
    }

    /**
     * Retrieve row click URL
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRateId()));
    }

}
