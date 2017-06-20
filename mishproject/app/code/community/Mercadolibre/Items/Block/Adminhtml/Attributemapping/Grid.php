<?php

class Mercadolibre_Items_Block_Adminhtml_Attributemapping_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('ittributemappingGrid');
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

	 	//get the store name
		if($this->getRequest()->getParam('store')){
			$store_id = $this->getRequest()->getParam('store');
		} else {
			$store_id = Mage::helper('items')-> getMlDefaultStoreId();
		}
		$attribute_id = '';
		if($this->getRequest()->getParam('attribute_id')!=''){
			$attribute_id = $this->getRequest()->getParam('attribute_id');
		} 
		
		$category_id = 0;
		if($this->getRequest()->getParam('category_id')){
			$category_id = $this->getRequest()->getParam('category_id');
		}
		
		$collection =  Mage::getModel('eav/entity_attribute_option')
					-> getCollection()
					-> setStoreFilter($store_id)
					-> join('attribute','attribute.attribute_id=main_table.attribute_id', 'attribute_code')
					-> addFieldToFilter('attribute.attribute_id',$attribute_id)
					-> addFieldToFilter('tsv.value', array('neq' => 'NULL' ));			
					
      $collection -> getSelect()
				  -> joinleft(array('mavm'=>'mercadolibre_attribute_value_mapping'), " main_table.option_id = mavm.mage_attribute_option_id AND mavm.sort_order = '0' AND mavm.category_id = '".$category_id."'  AND mavm.store_id = '".$store_id."'",array('mavm.*'));
		  
				  
				  								
	  $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
  
	   $this->addColumn('mage_attribute_value', array(
          'header'    => Mage::helper('items')->__('Magento Attribute Value'),
          'align'     =>'left',
          'index'     => 'value',
		  'renderer'  => 'items/adminhtml_attributemapping_renderer_hidden',
		  'filter'  => false, 
		  'sortable'  => false
      ));
  
	  $this->addColumn('meli_value_id', array(
          'header'    => Mage::helper('items')->__('Meli Value'),
          'align'     =>'left',
          'index'     => 'meli_value_id',
		  'renderer'  => 'items/adminhtml_attributemapping_renderer_hidden',
		  'filter'  => false, 
		  'sortable'  => false
      ));

      
		$this->addExportType('*/*/exportCsv', Mage::helper('items')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('items')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('meli_category_name');

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