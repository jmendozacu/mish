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

class MageWorx_Deliveryzone_Block_Adminhtml_Manager_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
	    parent::__construct();
	    $this->setId('categoriesGrid');
	    $this->setDefaultSort('title');
	    $this->setDefaultDir('ASC');
	    $this->setSaveParametersInSession(true);
	}

        private function _getStatusses() {
            return array(
                    1 => Mage::helper('deliveryzone')->__('Active'),
                    0 => Mage::helper('deliveryzone')->__('Inactive')
                );
        }
    
	protected function _prepareCollection()
	{
	    $collection = Mage::getModel('deliveryzone/zone')->getCollection();
            $collection->addCalculation();
            $this->setCollection($collection);
	    return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('title', array(
		    'header'    => Mage::helper('deliveryzone')->__('Title'),
		    'align'     => 'left',
		    'index'     => 'title',
                    
		));
		 
		$this->addColumn('countries', array(
		    'header'    => Mage::helper('deliveryzone')->__('Countries'),
		    'align'     => 'left',
		    'index'     => 'countries',
                    'filter'    => false,
                    'style'     => 'width:80px',
		));
		 
		$this->addColumn('categories', array(
		    'header'    => Mage::helper('deliveryzone')->__('Categories'),
		    'align'     => 'left',
		    'index'     => 'categories',
                    'filter'    => false,
		));
		 
		$this->addColumn('products', array(
		    'header'    => Mage::helper('deliveryzone')->__('Products'),
		    'align'     => 'left',
		    'index'     => 'products',
                    'filter'    => false,
		));

                $this->addColumn('status', array(
		    'header'    => Mage::helper('deliveryzone')->__('Status'),
		    'align'     => 'left',
		    'index'     => 'status',
                    'filter'    => false,
                    'width'     => '120px',
                    'type'      => 'options',
                    'options'   => $this->_getStatusses(),
		));

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
		        'index'     => 'stores',
		        'is_system' => true,
		));

	    return parent::_prepareColumns();
	}

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
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

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}