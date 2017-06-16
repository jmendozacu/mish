<?php

class Mercadolibre_Items_Block_Adminhtml_Categorymapping_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
        parent::__construct();
        $this->setId('itemsGrid');
        $this->setDefaultSort('category_id');
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

		$collection =  Mage::getModel('items/melicategoriesmapping')
					-> getCollection()
					-> addFieldToFilter('main_table.store_id', $this->_getStore()->getId()) 	
					-> addFieldToFilter('eav_entity_type.entity_type_code', 'catalog_category');
					//-> addFieldToFilter('e.children_count', '0');
		$collection -> getSelect()
					-> join(array('e'=>'catalog_category_entity'),'e.entity_id = main_table.mage_category_id')
					-> join(array('at_is_active'=>'catalog_category_entity'),'at_is_active.entity_id = e.entity_id')
					-> join(array('eav_entity_type'=>'eav_entity_type'),'eav_entity_type.entity_type_id = e.entity_type_id',array('eav_entity_type.entity_type_code'))
				    -> joinleft(array('mc' => 'mercadolibre_categories'), 'mc.meli_category_id = main_table.meli_category_id ', array("if(main_table.meli_category_id = 'NO_MAPPING','No Mapping', mc.meli_category_name) as meliCatName","if(mc.has_attributes = 1,'Yes','NO') as has_attributes", 'mc.category_id as mc_category_id'))
					-> joinleft(array('mcf' => 'mercadolibre_categories_filter'), "mcf.meli_category_id = main_table.meli_category_id AND mcf.store_id ='".$this->_getStore()->getId()."'" , array('mcf.meli_category_path'));
		
		$this->setCollection($collection);
        return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
  
   $this->addColumn('mage_category_id', array(
        'header'    => Mage::helper('items')->__('Mage Cat ID'),
        'align'     =>'right',
        'width'     => '10px',
        'index'     => 'mage_category_id',          
       )); 
	
     
    $this->addColumn('mapping_id', array(
        'header'    => Mage::helper('items')->__('Magento Category'),
        'align'     =>'left',
        'type'      => 'input',
		'sortable'  => false,
        'renderer'  => 'items/adminhtml_categorymapping_renderer_input', 
		'filter_condition_callback' => array($this, '_categoryFilter')
    ));

   	
    $this->addColumn('meliCatName', array(
        'header'    => Mage::helper('sales')->__('Mapped MercadoLibre Category'),
        'index'     => 'meliCatName',
        'width'     => '20%',
		'renderer'  => 'items/adminhtml_categorymapping_renderer_input', 
		'sortable'  => false,
		'filter_condition_callback' => array($this, '_mlCategoryFilter')
    )); 
	
	 $this->addColumn('has_attributes', array(
			'header'    => Mage::helper('sales')->__('Variation'),
			'index'     => 'has_attributes',
			'width'     => '17%',
			'renderer'  => 'items/adminhtml_categorymapping_renderer_input',  
			'sortable'  => false,
			'filter'    => false,
    )); 

    $this->addColumn('meli_category_id', array(
        'header'    => Mage::helper('sales')->__('MercadoLibre Category'),
        'index'     => 'meli_category_id',
        'width'     => '33%',
       // 'type'      => 'select',
       //'options'   => $optionsMeliCat,
        'filter'    => false,
        'sortable'  => false,
		'renderer'  => 'items/adminhtml_categorymapping_renderer_input', 
    ));

    $this->addExportType('*/*/exportCsv', Mage::helper('items')->__('CSV'));  
      return parent::_prepareColumns();
  }
  protected function _categoryFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
 
        $cat = Mage::getResourceModel('catalog/category_collection')->addAttributeToFilter('name', array('like' =>"%$value%" ));
		$mageArr = $cat->getData();
		if(count($mageArr)>0){
			foreach($mageArr as $key=>$mcat){
				$mageCatArr[] = $mcat['entity_id'];
			}
		}
		$this->getCollection()
			 -> addFieldToFilter('main_table.mage_category_id', array('IN' => $mageCatArr));
		return $this;
    }

	protected function _mlCategoryFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
 		$this->getCollection()
			 -> addFieldToFilter('mc.meli_category_name', array('like' =>"%$value%" ));
		return $this;
    }

  public function getRowUrl($row)
  {
      //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
      return '';
  }

}