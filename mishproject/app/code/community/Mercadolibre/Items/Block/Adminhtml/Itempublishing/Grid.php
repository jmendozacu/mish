<?php

class Mercadolibre_Items_Block_Adminhtml_Itempublishing_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $storeId = (int) $this->getRequest()->getParam('store', Mage::helper('items')-> getMlDefaultStoreId());
        return Mage::app()->getStore($storeId);
    }
  
  protected function _prepareCollection()
  { 	
  		$revise = '';	
  		if($this->getRequest()->getParam('revise')){
  			$revise = (int) $this->getRequest()->getParam('revise'); // product_id to revise published product
		}
		
		/* get Filter CategoryIds*/
		$commonModel = Mage::getModel('items/common');
		
		if($this->getRequest()->getParam('root_id')){
			$rootId = $this->getRequest()->getParam('root_id');
		} else {
			$rootId = Mage::app()->getStore($this->_getStore()->getId())->getRootCategoryId();
		}

		$FilterCategory =  $commonModel->getMageFilterCategoryIds($rootId);
		 
		$collection 	=  Mage::getModel('items/mercadolibreitem')->getCollection()
						-> addFieldToFilter('main_table.store_id', $this-> _getStore()->getId());
						//-> addFieldToFilter('main_table.category_id', array('in' => $FilterCategory));
						
		if($revise){
			$collection -> addFieldToFilter('main_table.product_id', $revise);
		}
		
		$collection -> getSelect()
					-> joinleft(array('mapping'=>'mercadolibre_categories_mapping'), "main_table.category_id = mapping.mage_category_id AND mapping.store_id = '".$this-> _getStore()->getId()."'", array('mapping.meli_category_id','mapping.store_id'))
					-> joinleft(array('melicat'=>'mercadolibre_categories'), 'mapping.meli_category_id = melicat.meli_category_id',array('melicat.meli_category_name','melicat.has_attributes as has_attributes',"if(main_table.meli_item_id !='',main_table.meli_category_id,melicat.meli_category_id) as meli_category_id"))
					-> joinleft(array('mmt'=>'mercadolibre_master_templates'), "main_table.master_temp_id = mmt.master_temp_id", array('mmt.master_temp_title','mmt.template_id','mmt.shipping_id','mmt.profile_id', 'mmt.payment_id as payment_method_id'))
					-> joinleft(array('mltemplate'=>'mercadolibre_product_templates'), "mmt.template_id = mltemplate.template_id", array('mltemplate.buying_mode_id','mltemplate.listing_type_id','mltemplate.condition_id', 'mltemplate.title as template_name'))
					-> joinleft(array('mlshipping'=>'mercadolibre_shipping'), "mmt.shipping_id = mlshipping.shipping_id", array('mlshipping.shipping_profile'))
					-> joinleft(array('mlipd'=>'mercadolibre_item_profile_detail'), "mmt.profile_id = mlipd.profile_id", array('mlipd.description_header','mlipd.description_body','mlipd.description_footer'));
		$collection -> getSelect()-> limit('20');
									
		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      
	  	  $this->addColumn('check_box', array(
				'header'    => Mage::helper('items')->__('Select'),
				'align'     =>'right',
				'width'     => '20px',
				'index'     => 'check_box',
				'renderer'  => 'items/adminhtml_itempublishing_renderer_hidden',
				'filter'  => false, 
				'sortable'  => false,  
		));
	  
	  $this->addColumn('product_id', array(
			  'header'    => Mage::helper('items')->__('ID'),
			  'align'     =>'right',
			  'width'     => '50px',
			  'index'     => 'product_id',

      ));
	   $this->addColumn('meli_item_id', array(
			  'header'    => Mage::helper('items')->__('Meli Item ID'),
			  'align'     =>'right',
			  'index'     => 'meli_item_id',
			  'renderer'  => 'items/adminhtml_itempublishing_renderer_hidden',  
			  'filter'	=> false, 
			  'sortable'  => false,  
      ));
	  
	 /* $this->addColumn('image', array(
				'header'=> Mage::helper('catalog')->__('Image'),
				'align'     =>'left',
				'index'     => 'pictures',
				'type'      => 'input',
				'filter'	=> false, 
				'sortable'  => false,  
				'renderer'  => 'items/adminhtml_itempublishing_renderer_productimage'  
		));*/
		
		 $this->addColumn('image', array(
				'header'=> Mage::helper('catalog')->__('Image'),
				'align'     =>'left',
				'width' 	=>'160px',
				'index'     => 'product_id',
				'type'      => 'input',
				'filter'	=> false, 
				'sortable'  => false,  
				'renderer'  => 'items/adminhtml_itempublishing_renderer_productimage'  
		));
	  
	  $this->addColumn('main_table.title', array(
                'header'=> Mage::helper('catalog')->__('Product Name'),
                'align'     =>'left',
                'index'     => 'title',
       ));
	   $this->addColumn('variation',array(
				'header'=> Mage::helper('catalog')->__('Variation'),
				'align'     =>'left',
				'index'     => 'variation',
				'type'      => 'input',
				'filter'	=> false, 
				'sortable'  => false,  
				'renderer'  => 'items/adminhtml_itempublishing_renderer_hidden'
		)); 
        /*$this->addColumn('descriptions', array(
				'header'=> Mage::helper('catalog')->__('Descriptions'),
				'align'     =>'left',
				'index' => 'descriptions',
				'width' =>'250px',
				'filter'	=> false, 
				'sortable'  => false,  
				'renderer'  => 'items/adminhtml_itempublishing_renderer_hidden'     

     ));*/
	    
      $store = $this->_getStore();  
	  $this->addColumn('meli_category_id', array(
				'header'    => Mage::helper('items')->__('Meli Category'),
				'align'     =>'left',
				'width' =>'80px',
				'index'     => 'meli_category_id',
				'type'      => 'input',
				'sortable'  => false, 
				'filter_condition_callback' => array($this, '_catsPubFilter'), 
				'renderer'  => 'items/adminhtml_itempublishing_renderer_hidden'    
      ));
          
     if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
		$this->addColumn('meli_quantity',array(
					'header'=> Mage::helper('catalog')->__('Start / Stop Time'),
					'align'     => 'left',
					'class'     => 'validate-number',
					'index'     => 'qty',
					'type'      => 'input',
					'renderer'  => 'items/adminhtml_itempublishing_renderer_hidden', 
					'filter'	=> false, 
				    'sortable'  => false,  
		));
	}

		$this->addColumn('profiles',array(
				'header'=> Mage::helper('catalog')->__('Profiles'),	
				'align'     =>'left',
				'index'     => 'profiles',
				'width' =>'180px',
				'renderer'  => 'items/adminhtml_itempublishing_renderer_hidden',
				'filter'    => false,
				'sortable'  => false,		
		)); 

      return parent::_prepareColumns();
  }
    protected function _catsPubFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
 		$this->getCollection()
			 -> addFieldToFilter('melicat.meli_category_name', array('like' =>"%$value%" ));
		return $this;
    }


    protected function _prepareMassaction()
    {
       // $this->setMassactionIdField('product_id');
//        $this->getMassactionBlock()->setFormFieldName('itempublishing[]');
//
//        $this->getMassactionBlock()->addItem('delete', array(
//             'label'    => Mage::helper('items')->__('Delete'),
//             'url'      => $this->getUrl('*/*/massDelete'),
//             'confirm'  => Mage::helper('items')->__('Are you sure?')
//        ));
//
//       $statuses = Mage::getSingleton('items/status')->getOptionArray();
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
        return ''; // $this;
    }

  public function getRowUrl($row)
  {
       return '';//$this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}