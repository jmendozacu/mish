<?php

class Mercadolibre_Items_Block_Adminhtml_Itemtemplates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
	  parent::__construct();
      $this->setId('itemlistingGrid');
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
		/*if($this->getRequest()->getParam('store'))
	  	{
			$storeId = (int) $this->getRequest()->getParam('store');
		} else if(Mage::helper('items')-> getMlDefaultStoreId()){
			$storeId = Mage::helper('items')-> getMlDefaultStoreId();
		} else {
			$storeId = $this->getStoreId();
		}*/
		$collection 	= 	Mage::getModel('items/meliproducttemplates')->getCollection();
						//-> addFieldToFilter('store_id',$storeId);
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('template_id', array(
			  'header'    => Mage::helper('items')->__('ID'),
			  'align'     =>'right',
			  'width'     => '50px',
			  'index'     => 'template_id',

      ));
  
	  $this->addColumn('title', array(
                'header'=> Mage::helper('catalog')->__('Profile Name'),
                'align'     =>'left',
                'index'     => 'title',
       ));
	   
        $CommonModel = Mage::getModel('items/common');
        $optionsBuyingType = $CommonModel->getBuyingType(); 
        $optionsListingType = $CommonModel->getListingType(); 
        $optionsCondition = $CommonModel->getCondition(); 
         $this->addColumn('buying_mode_id',array(
					'header'=> Mage::helper('catalog')->__('Buying Type'),		
					'align'     =>'left',
					'index'     => 'buying_mode_id',
					'type'      => 'input',
					'renderer'  => 'items/adminhtml_itemtemplates_renderer_hidden',
					'filter' 	=>false		
			
	)); 
         
	 $this->addColumn('listing_type_id',array(
				'header'=> Mage::helper('catalog')->__('Listing Type'),		
				'align'     =>'left',
				'index'     => 'listing_type_id',
				'type'      => 'input',
				'renderer'  => 'items/adminhtml_itemtemplates_renderer_hidden',
				'filter' 	=>false				
		
	)); 
	 $this->addColumn('condition_id',array(
				'header'=> Mage::helper('catalog')->__('Condition'),		
				'align'     =>'left',
				'index'     => 'condition_id',
				'type'      => 'input',
				'renderer'  => 'items/adminhtml_itemtemplates_renderer_hidden',
				'filter' 	=>false			
		
	)); 
	
	 $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('items')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('items')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
	
	
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('template_id');
        $this->getMassactionBlock()->setFormFieldName('itemtemplates');

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
       return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}