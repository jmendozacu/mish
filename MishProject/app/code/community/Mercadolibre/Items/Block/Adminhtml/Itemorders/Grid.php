<?php

class Mercadolibre_Items_Block_Adminhtml_Itemorders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('itemordersGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('items/mercadolibreorder')->getCollection()
	  			  -> addFieldToFilter('main_table.store_id',Mage::helper('items')->_getStore()->getId());	
	  $collection -> getSelect()
				  -> joinleft(array('mlbuyer'=>'mercadolibre_buyer'), "mlbuyer.buyer_id = main_table.buyer_id", array('mlbuyer.nickname as buyer_nickname', 'mlbuyer.email as buyer_email', 'CONCAT_WS(" ", `first_name`, `last_name`) as buyer_Name', "FORMAT(total_amount, 2) as total_amount"));
				
	  $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
	 $this->addColumn('sale_date', array(
            'header' => Mage::helper('sales')->__('Sale Date'),
            'index' => 'date_created',
            'type' => 'datetime',
            'width' => '100px',
			'sortable'  => false,
        ));
	  
	  $this->addColumn('mage_order_Id', array(
          'header'    => Mage::helper('items')->__('Mage Order ID'),
          'align'     =>'left',
          'width'     => '50px',
		  'sortable'  => false,
          'index'     => 'mage_order_Id',
		  'renderer'  => 'items/adminhtml_itemorders_renderer_input',
      ));
	 
	 $this->addColumn('order_id', array(
          'header'    => Mage::helper('items')->__('Meli Order ID'),
          'align'     =>'left',
          'width'     => '100px',
		  'sortable'  => false,
          'index'     => 'order_id',
		  'renderer'  => 'items/adminhtml_itemorders_renderer_input',
      ));
	 $this->addColumn('variation', array(
          'header'    => Mage::helper('items')->__('Items'),
          'align'     =>'left',
          'sortable'  => false,
		  'renderer'  => 'items/adminhtml_itemorders_renderer_input',
		  'filter_condition_callback' => array($this, '_itemsFilter')
      ));
	  $this->addColumn('email', array(
          'header'    => Mage::helper('items')->__('Buyer'),
          'align'     =>'left',
          'index'     => 'email',
		  'sortable'  => false,
		  'renderer'  => 'items/adminhtml_itemorders_renderer_input'
      ));
	  
	  $this->addColumn('total_amount', array(
          'header'    => Mage::helper('items')->__('Total Amount'),
          'align'     =>'left',
          'index'     => 'total_amount',
		  'sortable'  => false,
		  'type'  => 'currency', 
      ));
	  
	$this->addColumn('payment_status', array(
            'header' => Mage::helper('sales')->__('Paid'),
            'index' => 'payment_status',
            'type'  => 'options',
            'width' => '100px',
			'options' => array('No', 'Yes'),
			'sortable'  => false,
        ));
      $this->addColumn('shipping_status', array(
            'header' => Mage::helper('sales')->__('Shipped'),
            'index' => 'shipping_status',
            'type'  => 'options',
            'width' => '100px',
			'options' => array('No', 'Yes'),
			'sortable'  => false,
        ));
	 return parent::_prepareColumns();
  }
  protected function _itemsFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
 
 		$itemDetail = Mage::getModel('items/meliorderitems')->getCollection()
								-> addFieldToFilter('title', array('like' =>"%$value%" ))
								-> addFieldToSelect('order_id');
		$orderIds = $itemDetail->getData();
		if(count($orderIds)>0){
			foreach($orderIds as $key=>$morder){
				$mageOrderArr[] = $morder['order_id'];
			}
		}
		$this->getCollection()
			 -> addFieldToFilter('main_table.order_id', array('IN' => $mageOrderArr));
		return $this;
    }

     protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('orderid');

        $this->getMassactionBlock()->addItem('paid', array(
             'label'    => Mage::helper('items')->__('Mark Order(s) as Paid'),
             'url'      => $this->getUrl('*/*/massMlOrderPaid')
            
        ));
		 $this->getMassactionBlock()->addItem('shipped', array(
             'label'    => Mage::helper('items')->__('Mark Order(s) as Shipped'),
             'url'      => $this->getUrl('*/*/massMlOrderShipped')
             
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return ''; // $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}