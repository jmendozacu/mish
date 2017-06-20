<?php

class Mercadolibre_Items_Block_Adminhtml_Dashboard_Questions_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
      $collection = Mage::getModel('items/meliquestions')->getCollection()
	  ->setOrder('created_at', 'DESC');
	  //->limit( 5 );
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  
  protected function _preparePage()
    {
        $this->getCollection()->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
        // Remove count of total orders $this->getCollection()->setCurPage($this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

  protected function _prepareColumns()
  {
      /*$this->addColumn('id', array(
          'header'    => Mage::helper('items')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));*/
      $this->addColumn('itemid', array(
          'header'    => Mage::helper('items')->__('Item ID'),
          'align'     =>'left',
          'index'     => 'itemid',
      ));
      $this->addColumn('question_id', array(
          'header'    => Mage::helper('items')->__('Question ID'),
          'align'     =>'left',
          'index'     => 'question_id',
      ));
      $this->addColumn('question', array(
          'header'    => Mage::helper('items')->__('Question'),
          'align'     =>'left',
          'index'     => 'question',
      ));	
     $this->addColumn('question_date', array(
          'header'    => Mage::helper('items')->__('Question Date'),
          'align'     =>'left',
		   'width'     => '20px',
          'index'     => 'question_date',
      ));
     /*$this->addColumn('buyer', array(
          'header'    => Mage::helper('items')->__('Buyer'),
          'align'     =>'left',
          'index'     => 'buyer',
      ));*/
     
     $this->addColumn('answer', array(
          'header'    => Mage::helper('items')->__('Answer'),
          'align'     =>'left',
          'index'     => 'answer',
      ));
     /*$this->addColumn('created_at', array(
          'header'    => Mage::helper('items')->__('Created at'),
          'align'     =>'left',
          'index'     => 'created_at',
      )); */

      $this->addColumn('status', array(
          'header'    => Mage::helper('items')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              'ANSWERED' => 'ANSWERED',
              'UNANSWERED' => 'UNANSWERED',
          ),
      ));
	 
	  $this->setFilterVisibility(false);
      $this->setPagerVisibility(false); 
	return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
         return '';
    }

  public function getRowUrl($row)
  {
      return ''; //$this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}