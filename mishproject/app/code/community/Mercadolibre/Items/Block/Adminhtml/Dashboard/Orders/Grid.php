<?php

class Mercadolibre_Items_Block_Adminhtml_Dashboard_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
      $collection = Mage::getModel('items/mercadolibreorder')->getCollection();
	  $collection -> getSelect()
				  -> joinleft(array('mlbuyer'=>'mercadolibre_buyer'), "main_table.buyer_id = mlbuyer.buyer_id", array('mlbuyer.buyer_id', 'mlbuyer.nickname as buyer_nickname', 'mlbuyer.email as buyer_email', 'CONCAT_WS(" ", `first_name`, `last_name`) as buyer_Name', "FORMAT(total_amount, 2) as total_amount"))
				  ->limit( 5 );
				  
	 $this->setCollection($collection);
	 return parent::_prepareCollection();
  }
  
  protected function _preparePage()
    {
        $this->getCollection()->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
        // Remove count of total orders $this->getCollection()->setCurPage($this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

  protected function _prepareColumns()
  {
	$this->addColumn('order_id', array(
          'header'    => Mage::helper('items')->__('Meli Order ID'),
          'align'     =>'left',
          'width'     => '50px',
          'index'     => 'order_id',
      ));
	 $this->addColumn('variation', array(
          'header'    => Mage::helper('items')->__('Item Details'),
          'align'     =>'left',
         // 'width'     => '200px',
          'index'     => 'variation',
		  'filter'  => false, 
		  'sortable'  => false,
		  'renderer'  => 'items/adminhtml_itemorders_renderer_input'
      ));
	  $this->addColumn('status', array(
          'header'    => Mage::helper('items')->__('Status'),
          'align'     =>'left',
          'index'     => 'status',
		  'width'     => '265px',
		  'filter'  => false, 
		  'sortable'  => false,
		  'renderer'  => 'items/adminhtml_itemorders_renderer_input'
      ));
	  
	 $this->addColumn('total_amount', array(
          'header'    => Mage::helper('items')->__('Total Amount'),
          'align'     =>'left',
          'index'     => 'total_amount',
		  'filter'  => false, 
		  'sortable'  => false,  
      ));
	  $this->addColumn('order_date', array(
          'header'    => Mage::helper('items')->__('Date'),
          'align'     =>'left',
          'index'     => 'date_created',
		  'filter'  => false, 
		  'sortable'  => false,
		  'width'     => '150px',
		  'renderer'  => 'items/adminhtml_itemorders_renderer_input'
      ));
	  
	   $this->setFilterVisibility(false);
       $this->setPagerVisibility(false);
	  
	 return parent::_prepareColumns();
  }
  
  protected function _prepareMassaction()
    {
	 return $this;
    }

  public function getRowUrl($row)
  {
      return ''; // $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}