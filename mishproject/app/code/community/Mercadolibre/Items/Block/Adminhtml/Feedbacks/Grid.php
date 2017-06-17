<?php

class Mercadolibre_Items_Block_Adminhtml_Feedbacks_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('id');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
		$storeId = Mage::helper('items')-> _getStore()->getId();
		$orderId = $this->getRequest()->getParam('order',0);
        $collection = Mage::getModel('items/melifeedbacks')->getCollection()
					-> setOrder('id', 'ASC');
		$collection->getSelect()
					-> joinleft(array('mlorder'=>'mercadolibre_order'), "mlorder.order_id = main_table.order_id", array('mlorder.store_id'));
		$collection -> addFieldToFilter('mlorder.store_id',$storeId)
					-> setOrder('main_table.id', 'ASC');
		if($orderId){
			$collection	-> addFieldToFilter('mlorder.order_id',$orderId);    
		}
		$this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('items')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
        ));
        $this->addColumn('order_id', array(
            'header' => Mage::helper('items')->__('Order ID'),
            'align' => 'left',
            'index' => 'order_id',
        ));
        $this->addColumn('rating', array(
            'header' => Mage::helper('items')->__('Rating'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'rating',
            'type' => 'options',
            'options' => array(
                'positive' => 'positive',
                'neutral' => 'neutral',
                'negative' => 'negative',
            ),
        ));
        $this->addColumn('fulfilled', array(
            'header' => Mage::helper('items')->__('Fulfilled'),
            'align' => 'left',
            'index' => 'fulfilled',
        ));
        $this->addColumn('reason', array(
            'header' => Mage::helper('items')->__('Reason'),
            'align' => 'left',
            'index' => 'reason',
        ));
        $this->addColumn('message', array(
            'header' => Mage::helper('items')->__('Message'),
            'align' => 'left',
            'index' => 'message',
        ));

        $this->addColumn('reply', array(
            'header' => Mage::helper('items')->__('Reply'),
            'align' => 'left',
            'index' => 'reply',
        ));
		 $this->addColumn('rating_seller', array(
            'header' => Mage::helper('items')->__('Seller\'s Rating'),
             'align' => 'left',
            'width' => '80px',
            'index' => 'rating_seller',
            'type' => 'options',
            'options' => array(
                'positive' => 'positive',
                'neutral' => 'neutral',
                'negative' => 'negative',
            ),
        )); 
        $this->addColumn('date_created', array(
            'header' => Mage::helper('items')->__('Date created'),
            'align' => 'left',
            'index' => 'date_created',
        ));

 		$this->addColumn('action', array(
				'header'    => Mage::helper('items')->__('Action'),
				'align'     =>'right',
				'width'     => '100px',
				'type'      => 'action',
				//'index'     => 'entity_id',
				'renderer'  => 'items/adminhtml_feedbacks_renderer_hidden'
		));
		
		 return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        //$this->setMassactionIdField('id');
//        $this->getMassactionBlock()->setFormFieldName('feedback');
//
//        $this->getMassactionBlock()->addItem('delete', array(
//             'label'    => Mage::helper('items')->__('Delete'),
//             'url'      => $this->getUrl('*/*/massDelete'),
//             'confirm'  => Mage::helper('items')->__('Are you sure?')
//        ));
//
//        $statuses = Mage::getSingleton('items/status')->getOptionArray();
//
//        array_unshift($statuses, array('label'=>'', 'value'=>''));
//        $this->getMassactionBlock()->addItem('status', array(
//             'label'=> Mage::helper('items')->__('Change status'),
//             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//             'additional' => array(
//                    'visibility' => array(
//                         'name' => 'status',
//                         'type' => 'select',
//                         'class' => 'required-entry',
//                         'label' => Mage::helper('items')->__('Status'),
//                         'values' => $statuses
//                     )
//             )
//        ));
        return ''; //$this;
    }

    public function getRowUrl($row) {
        return ''; //$this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}