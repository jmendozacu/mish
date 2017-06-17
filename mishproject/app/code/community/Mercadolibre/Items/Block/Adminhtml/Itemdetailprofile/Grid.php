<?php

class Mercadolibre_Items_Block_Adminhtml_Itemdetailprofile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
	
	  parent::__construct();
      $this->setId('itemdetailprofileGrid');
      $this->setDefaultSort('profile_id');
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
		$collection 	= 	Mage::getModel('items/meliitemprofiledetail')->getCollection()
						-> addFieldToFilter('store_id',$storeId);
		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('profile_id', array(
			  'header'    => Mage::helper('items')->__('Profile ID'),
			  'align'     =>'right',
			  'width'     => '50px',
			  'index'     => 'profile_id'

      ));
  
	  $this->addColumn('profile_name', array(
                'header'=> Mage::helper('items')->__('Profile Name'),
                'align'     =>'left',
                'index'     => 'profile_name'
       ));

/*	  $this->addColumn('product_name', array(
                'header'=> Mage::helper('items')->__('Product Name'),
                'align'     =>'left',
                'index'     => 'product_name'				
       ));*/
	  
        $this->addColumn('descriptions', array(
				'header'=> Mage::helper('catalog')->__('Descriptions'),
				'align'     =>'left',
				'index' => 'descriptions',
				'width' =>'300px',
				'filter'	=> false, 
				'sortable'  => false,  
				'renderer'  => 'items/adminhtml_itemdetailprofile_renderer_custom'     

     ));
	  
	   $this->addColumn('date_created', array(
                'header'=> Mage::helper('items')->__('Creation Date'),
                'align'     =>'left',
                'index'     => 'date_created',
				'filter'	=> false, 
				'sortable'  => false,
       ));

	  $this->addColumn('date_updated', array(
                'header'=> Mage::helper('items')->__('Updation Date'),
                'align'     =>'left',
                'index'     => 'date_updated',
				'filter'	=> false, 
				'sortable'  => false,
       ));
	   
	   $this->addColumn('action', array(
				'header'    => Mage::helper('items')->__('Action'),
				'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
				'index'     => 'stores',
				'filter'    => false,
                'sortable'  => false,
				'renderer'  => 'items/adminhtml_itemdetailprofile_renderer_custom'
		));
		
	  
	
	
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('profile_id');
        $this->getMassactionBlock()->setFormFieldName('itemdetailprofile'); 

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('items')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('items')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('items/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
       
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