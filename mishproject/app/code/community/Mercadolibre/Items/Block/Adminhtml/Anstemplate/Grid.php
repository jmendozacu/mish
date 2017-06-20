<?php

class Mercadolibre_Items_Block_Adminhtml_Anstemplate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('id');
      $this->setDefaultSort('itemid');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
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
	  $collection =  Mage::getModel('items/melianswertemplate')->getCollection()
	  			  -> addFieldToFilter('store_id',$storeId)
				  -> setOrder('created_date', 'DESC');
				  
	  $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('answer_template_id', array(
          'header'    => Mage::helper('items')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'answer_template_id',
		  //'filter'  => false, 
		  'sortable'  => false,  
      ));
      $this->addColumn('title', array(
          'header'    => Mage::helper('items')->__('Template Title'),
          'align'     =>'left',
          'index'     => 'title',
		  'sortable'  => false,  
      ));
      $this->addColumn('answer', array(
          'header'    => Mage::helper('items')->__('Answer'),
          'align'     =>'left',
          'index'     => 'answer',
		  'sortable'  => false,
      ));
      
     
     $this->addColumn('created_date', array(
          'header'    => Mage::helper('items')->__('Created at'),
          'align'     =>'left',
          'index'     => 'created_date',
      ));

      
	$this->addColumn('action', array(
			'header'    => Mage::helper('items')->__('Action'),
			'align'     =>'right',
			'width'     => '50px',
			'type'      => 'action',
			'sortable'  => false,
			'filter'  => false,   
			'renderer'  => 'items/adminhtml_anstemplate_renderer_hidden'
	));
	  return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        return '';
    }

  public function getRowUrl($row)
  {
      return '';
  }

}