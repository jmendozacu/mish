<?php
class Mercadolibre_Items_Block_Adminhtml_Itemlisting_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $storeId = (int) $this->getRequest()->getParam('store',Mage::helper('items')-> getMlDefaultStoreId());
        return Mage::app()->getStore($storeId);
    }
  
  protected function _prepareCollection()
  {
		
		$store = $this->_getStore();
		/* Get Filter CategoryIds*/
		$commonModel = Mage::getModel('items/common');
		/* Product Collection  */

		/* Join to get Product Categories & categories filrter  */
		if($this->getRequest()->getParam('root_id')){
			 $rootcatId = $this->getRequest()->getParam('root_id'); 
		 } else {
		 	$rootcatId = Mage::app()->getStore($this->_getStore()->getId())->getRootCategoryId();  
		 }

		 
		$FilterCategory =  $commonModel->getMageFilterCategoryIds($rootcatId); 
		//$FilterProducts =  $commonModel->getMageFinalProducts($rootcatId);
		//$productTypeInlisting = Mage::getStoreConfig('mlitems/meliinventorysetting/producttypeinlisting',Mage::app()->getStore($this-> _getStore()->getId()));
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$productTypeInlisting = $db->fetchCol("SELECT value from core_config_data where path = 'mlitems/meliinventorysetting/producttypeinlisting' and scope_id = '".$this-> _getStore()->getId()."'");
		
		$productTypeToBeList = array('configurable');
		if(count($productTypeInlisting) > 0){
			$productTypeToBeList = explode(',',$productTypeInlisting['0']);
		}

        $collection = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToSelect('sku')
					->addAttributeToSelect('name')
					->addAttributeToSelect('attribute_set_id')
					->addAttributeToSelect('type_id')
					->setStoreId($this-> _getStore()->getId())
					//-> addAttributeToFilter('entity_id', array('in' => $FilterProducts))
					-> addAttributeToFilter('type_id', array('in' => $productTypeToBeList));
		$collection -> joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'inner')
					-> addAttributeToFilter('category_id', array('in' => $FilterCategory));
		$collection -> getSelect()-> limit('20');


		/* join to check category mapping */	
		$collection->joinTable('items/melicategoriesmapping','mage_category_id=category_id',array('meli_category_id','store_id'),"meli_category_id != '' AND meli_category_id != 'NO_MAPPING' AND mercadolibre_categories_mapping.store_id = '".$this-> _getStore()->getId()."'");
		/* Get Meli Category Name */	
		$collection -> getSelect()
					-> joinleft(array('melicat'=>'mercadolibre_categories'), 'mercadolibre_categories_mapping.meli_category_id = melicat.meli_category_id',array('melicat.shipping_modes','melicat.meli_category_name','melicat.category_id as mc_category_id','melicat.has_attributes as has_attributes'));
		/* Get Saved Listing data from Mercadolibre_item */
		$collection -> getSelect()
					-> joinleft(array('mlitem'=>'mercadolibre_item'), "e.entity_id = mlitem.product_id and mlitem.store_id = '".$this-> _getStore()->getId()."'", array("if(mlitem.meli_item_id !='',mlitem.meli_category_id,melicat.meli_category_id) as meli_category_id",'mlitem.title as meli_product_name','mlitem.price as meli_price','mlitem.available_quantity as meli_qty','mlitem.descriptions as meli_descriptions','mlitem.pictures as meli_images', 'mlitem.item_id as item_id','mlitem.sent_to_publish','mlitem.master_temp_id','mlitem.main_image'));
	
		if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            $collection -> setStoreId($store->getId());
			$collection -> addAttributeToFilter('visibility', array('neq' => 1));
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }else{
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner')
						-> addAttributeToFilter('visibility', array('neq' => 1));
		}

	  //$collection -> addFieldToFilter('melicat.meli_category_name', array('like' =>'TEST')); 
 	  //echo $collection -> getData()->getSelect(); die;
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
				'renderer'  => 'items/adminhtml_itemlisting_renderer_input',
				'filter'  => false, 
				'sortable'  => false,  
		));
	  	
		$this->addColumn('entity_id', array(
				'header'    => Mage::helper('items')->__('ID'),
				'align'     =>'right',
				'width'     => '50px',
				'index'     => 'entity_id',
				'renderer'  => 'items/adminhtml_itemlisting_renderer_hidden'
		));
		$this->addColumn('sent_to_publish',array(
				'header'=> Mage::helper('catalog')->__('Publishing <br />Status'),		
				'align'     =>'left',
				'index'     => 'sent_to_publish',
				'filter'    => false,
				'renderer'  => 'items/adminhtml_itemlisting_renderer_hidden',
		));
		  
		$this->addColumn('image', array(
				'header'=> Mage::helper('catalog')->__('Image'),
				'align'     =>'left',
				'width' =>'160px',
				'index'     => 'entity_id',
				'type'      => 'input',
				'renderer'  => 'items/adminhtml_itemlisting_renderer_productimage',
				'filter'	=> false, 
				'sortable'  => false,   
		));
		$this->addColumn('title',array(
				'header'=> Mage::helper('catalog')->__('Product Name'),
				'align'     =>'left',
				'index'     => 'name',
				'type'      => 'input',
				'renderer'  => 'items/adminhtml_itemlisting_renderer_input'
		));   
		$this->addColumn('variation',array(
				'header'=> Mage::helper('catalog')->__('Variation'),
				'align'     =>'left',
				'index'     => 'variation',
				'filter'	=> false, 
				'sortable'  => false,  
				'width' =>'120px',
				'type'      => 'input',
				'renderer'  => 'items/adminhtml_itemlisting_renderer_input'
		));
			
		$store = $this->_getStore();
/*		$this->addColumn('meli_category_name', array(
				'header'    => Mage::helper('items')->__('Meli Category'),
				'align'     =>'left',
				'width' =>'80px',
				'index'     => 'meli_category_name',
				'renderer'  => 'items/adminhtml_itemlisting_renderer_text',  
				'filter'	=> false, 
				'sortable'  => false,    
		));*/
		
		$this->addColumn('meli_category_id', array(
				'header'    => Mage::helper('items')->__('Meli Category'),
				'align'     =>'left',
				'width' =>'80px',
				'index'     => 'meli_category_id',
				'sortable'  => false, 
				'filter'	=> false, 
				//'filter_condition_callback' => array($this, '_catsListFilter'), 
				'renderer'  => 'items/adminhtml_itemlisting_renderer_text'    
		));
		$masterTempCollection = Mage::getModel('items/melimastertemplates')->getCollection();
		$melimasterTemplates = $masterTempCollection->getData();  
		$optionsMeliCat = array(''=>'Please Select Master Template');
		if(count($melimasterTemplates) > 0 ){
			foreach($melimasterTemplates as $rowtemp){
				$optionsMeliCat[$rowtemp['master_temp_id']] = $rowtemp['master_temp_title'];
			}
		}
		$this->addColumn('master_temp_id[]',array(
				'header'=> Mage::helper('catalog')->__('Profiles'),	
				'align'     =>'left',
				'index'     => 'master_temp_id',
				'width' =>'300px',
				//'type'      => 'select',
				'options'   => $optionsMeliCat,
				'filter'    => false,
				'sortable'  => false,	
				'type'      => 'input',
				'renderer'  => 'items/adminhtml_itemlisting_renderer_input'	
		));

	  return parent::_prepareColumns();
	  }
	  
	/*protected function _catsListFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
 
 		$this->getCollection()
			 -> addFieldToFilter('melicat.meli_category_name', array('like' =>"%$value%" ));
		return $this;
    }*/
    protected function _prepareMassaction()
    {
       // $this->setMassactionIdField('item_id');
//        $this->getMassactionBlock()->setFormFieldName('itemlisting[]');
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
        return $this;
    }

  public function getRowUrl($row)
  {
       return '';//$this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}