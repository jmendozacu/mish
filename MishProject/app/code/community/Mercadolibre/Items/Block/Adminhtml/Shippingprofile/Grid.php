<?php

class Mercadolibre_Items_Block_Adminhtml_Shippingprofile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
	  parent::__construct();
      $this->setId('shippingprofileGrid');
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
		if($this->getRequest()->getParam('store'))
	  	{
			$storeId = (int) $this->getRequest()->getParam('store');
		} else if(Mage::helper('items')-> getMlDefaultStoreId()){
			$storeId = Mage::helper('items')-> getMlDefaultStoreId();
		} else {
			$storeId = $this->getStoreId();
		}
		$collection = Mage::getModel('items/melishipping')->getCollection()
					-> addFieldToFilter('store_id',$storeId);
		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('shipping_id', array(
			  'header'    => Mage::helper('items')->__('ID'),
			  'align'     =>'right',
			  'width'     => '50px',
			  'index'     => 'shipping_id',

      ));
  
	  $this->addColumn('shipping_profile', array(
                'header'=> Mage::helper('catalog')->__('Shipping Profile'),
                'align'     =>'left',
                'index'     => 'shipping_profile',
       ));
	   
        $CommonModel = Mage::getModel('items/common');
        $optionsBuyingType = $CommonModel->getBuyingType(); 
        $optionsListingType = $CommonModel->getListingType(); 
        $optionsCondition = $CommonModel->getCondition(); 
         $this->addColumn('shipping_mode',array(
					'header'=> Mage::helper('catalog')->__('Shipping Mode'),		
					'align'     =>'left',
					'index'     => 'shipping_mode',
					'filter' 	=>false		
			
	)); 
         
	 $this->addColumn('shipping_method',array(
				'header'=> Mage::helper('catalog')->__('Shipping Method'),		
				'align'     =>'left',
				'index'     => 'shipping_method',
				'filter' 	=>false				
		
	)); 
	 $this->addColumn('service_name_cost',array(
				'header'=> Mage::helper('catalog')->__('service Name / Cost '),		
				'align'     =>'left',
				'index'     => 'condition_id',
				'type'      => 'input',
				'renderer'  => 'items/adminhtml_shippingprofile_renderer_hidden',
				'filter' 	=>false			
		
	)); 
	
	$this->addColumn('action', array(
				'header'    => Mage::helper('items')->__('Action'),
				'align'     =>'right',
				'width'     => '100',
				'type'      => 'action',
				'filter'    => false,
                'sortable'  => false,
				'renderer'  => 'items/adminhtml_shippingprofile_renderer_hidden'
		));
	 return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('shipping_id');
        $this->getMassactionBlock()->setFormFieldName('shippingprofile');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('items')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('items')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('items/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('items')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('items')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      //get current store ID
	  if($this->getRequest()->getParam('store')){
			$storeId = (int) $this->getRequest()->getParam('store');
		} else if(Mage::helper('items')-> getMlDefaultStoreId()){
			$storeId = Mage::helper('items')-> getMlDefaultStoreId();
		} else {
			$storeId = $this->getStoreId();
		}
	   return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $storeId));
  }

}