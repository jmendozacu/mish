<?php

class Mercadolibre_Items_Block_Adminhtml_Questions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('question_id');
      $this->setDefaultSort('itemid');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
	  $storeId = Mage::helper('items')-> _getStore()->getId();
	  $collection = Mage::getModel('items/meliquestions')->getCollection();
	  $collection->getSelect()
	  			  	->join( array('mi' => 'mercadolibre_item'), 'mi.meli_item_id = main_table.itemid ', array("category_id","DATE_FORMAT(main_table.question_date,'%m-%d-%Y') as question_date","DATE_FORMAT(main_table.created_at,'%m-%d-%Y') as created_at"))
				 	->join( array('mcm' => 'mercadolibre_categories_mapping'), 'mcm.mage_category_id  = mi.category_id ', array("store_id"));
	  $collection -> addFieldToFilter('mcm.store_id',$storeId)  
	 			 ->setOrder('main_table.created_at', 'DESC');
	  $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('items')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));
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
     $this->addColumn('created_at', array(
          'header'    => Mage::helper('items')->__('Question Date'),
          'align'     =>'left',
		  'type'     =>'datetime',
          'index'     => 'created_at',
      ));
     $this->addColumn('buyer', array(
          'header'    => Mage::helper('items')->__('Buyer'),
          'align'     =>'left',
          'index'     => 'buyer',
      ));
     
     $this->addColumn('answer', array(
          'header'    => Mage::helper('items')->__('Answer'),
          'align'     =>'left',
          'index'     => 'answer',
      ));


      $this->addColumn('main_table.status', array(
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
	  
		$this->addColumn('action', array(
				'header'    => Mage::helper('items')->__('Action'),
				'align'     =>'right',
				'width'     => '50px',
				'type'      => 'action',
				//'index'     => 'entity_id',
				'renderer'  => 'items/adminhtml_questions_renderer_hidden'
		));
	  
       $this->addExportType('*/*/exportCsv', Mage::helper('items')->__('CSV'));
	   $this->addExportType('*/*/exportXml', Mage::helper('items')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('question_id');
		$this->getMassactionBlock()->setFormFieldName('question');
		$storeId =  Mage::helper('items')-> _getStore()->getId();
		$collection =  Mage::getModel('items/melianswertemplate')->getCollection()
				    -> addFieldToFilter('store_id',$storeId)
				    -> setOrder('created_date', 'DESC');
				  
		$allAnswers =  $collection->getData();
		$statuses = array();
		foreach($allAnswers as $data){
			$statuses[$data['answer_template_id']] = $data['title'];
		}
		$this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('items')->__('Bulk Answer'),
             'url'  => $this->getUrl('*/*/massAnswer', array('_current'=>true)),
			 'confirm'  => Mage::helper('items')->__('Please conform that you wants to give same reply for all selected questions.'),
             'additional' => array(
                    	 'visibility' => array(
                         'name' => 'ansTempId',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('items')->__('Select A Answer Template'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return ''; //$this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}