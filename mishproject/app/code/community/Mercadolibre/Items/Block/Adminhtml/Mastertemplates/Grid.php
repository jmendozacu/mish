<?php

class Mercadolibre_Items_Block_Adminhtml_Mastertemplates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
	  parent::__construct();
      $this->setId('masterlistingGrid');
      $this->setDefaultSort('master_temp_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  
  protected function _prepareCollection()
  { 
	    $store_id = Mage::helper('items')->_getStore()->getId();
		$collection =  Mage::getModel('items/melimastertemplates')-> getCollection()
					-> addFieldToFilter('main_table.store_id',$store_id);
		$collection -> getSelect()
					-> joinleft(array('mltemplate'=>'mercadolibre_product_templates'), "main_table.template_id = mltemplate.template_id", array('mltemplate.title'))
					-> joinleft(array('mlshipping'=>'mercadolibre_shipping'), "main_table.shipping_id = mlshipping.shipping_id", array('mlshipping.shipping_profile'))
					-> joinleft(array('mldescription'=>'mercadolibre_item_profile_detail'), "main_table.profile_id = mldescription.profile_id", array('mldescription.profile_name'));

		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('master_temp_id', array(
			  'header'    => Mage::helper('items')->__('ID'),
			  'align'     =>'right',
			  'width'     => '50px',
			  'index'     => 'master_temp_id',

      ));
	  $this->addColumn('master_temp_title', array(
			  'header'    => Mage::helper('items')->__('Master Template Title'),
			  'align'     =>'right',
			  'width'     => '50px',
			  'index'     => 'master_temp_title',
			  'sortable'  => false,

      ));
  
	  $this->addColumn('title', array(
                'header'=> Mage::helper('catalog')->__('Listing Type Template'),
                'align'     =>'left',
                'index'     => 'title',
				'filter' 	=>false,
				'sortable'  => false,
       ));
	   
      $this->addColumn('shipping_profile',array(
					'header'=> Mage::helper('catalog')->__('Shipping Template'),		
					'align'     =>'left',
					'index'     => 'shipping_profile',
					'filter' 	=>false,
					'sortable'  => false,	
			
	)); 
         
	 $this->addColumn('profile_name',array(
				'header'=> Mage::helper('catalog')->__('Description Template'),		
				'align'     =>'left',
				'index'     => 'profile_name',
				'filter' 	=>false	,
				'sortable'  => false,			
		
	)); 
	 $this->addColumn('paymentData',array(
				'header'=> Mage::helper('catalog')->__('Payment Template'),		
				'align'     =>'left',
				'renderer'  => 'items/adminhtml_mastertemplates_renderer_hidden',
				'filter' 	=>false,
				'sortable'  => false,		
		
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
		return $this;
    }

  public function getRowUrl($row)
  {
       return '';
  }

}