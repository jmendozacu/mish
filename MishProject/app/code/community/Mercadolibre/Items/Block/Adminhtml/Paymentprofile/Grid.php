<?php

class Mercadolibre_Items_Block_Adminhtml_Paymentprofile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
	  parent::__construct();
      $this->setId('paymentprofileGrid');
      $this->setDefaultSort('item_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

   protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
  
  protected function _prepareCollection()
  { 
		$collection = Mage::getModel('items/melipaymentmethods')->getCollection();
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
		$this->addColumn('id', array(
			  'header'    => Mage::helper('items')->__('ID'),
			  'align'     =>'left',
			  'width'     => '100px',
			  'index'     => 'id',
		));
		
		$this->addColumn('payment_id', array(
			'header'=> Mage::helper('catalog')->__('Payment ID'),
			'align'     =>'left',
			'index'     => 'payment_id',
		));
		
		$this->addColumn('payment_name', array(
			'header'=> Mage::helper('catalog')->__('Payment Name'),
			'align'     =>'left',
			'index'     => 'payment_name',
		));
		
		$this->addColumn('payment_type_id', array(
			'header'=> Mage::helper('catalog')->__('Payment Type ID'),
			'align'     =>'left',
			'index'     => 'payment_type_id',
		));
		
		$this->addColumn('thumbnail', array(
			'header'=> Mage::helper('catalog')->__('Thumbnail'),
			'align'     =>'left',
			'index'     => 'thumbnail',
		));
	   
	   $this->addColumn('secure_thumbnail', array(
			'header'=> Mage::helper('catalog')->__('Secure Thumbnail'),
			'align'     =>'left',
			'index'     => 'secure_thumbnail',
		));
    	return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
     }

  public function getRowUrl($row)
  {
       return ''; //$this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}