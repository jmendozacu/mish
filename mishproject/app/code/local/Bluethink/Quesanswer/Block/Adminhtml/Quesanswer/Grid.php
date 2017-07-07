<?php

class Bluethink_Quesanswer_Block_Adminhtml_Quesanswer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('quesanswerGrid');
      $this->setDefaultSort('quesanswer_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('quesanswer/quesanswer')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('quesanswer_id', array(
          'header'    => Mage::helper('quesanswer')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'quesanswer_id',
      ));

      $this->addColumn('sku', array(
          'header'    => Mage::helper('quesanswer')->__('Product Sku'),
          'align'     =>'left',
          'index'     => 'sku',
      ));

       $this->addColumn('question', array(
          'header'    => Mage::helper('quesanswer')->__('Question'),
          'align'     =>'left',
          'index'     => 'question',
      ));

        $this->addColumn('answer', array(
          'header'    => Mage::helper('quesanswer')->__('Answer'),
          'align'     =>'left',
          'index'     => 'answer',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('quesanswer')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

     /* $this->addColumn('status', array(
          'header'    => Mage::helper('quesanswer')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));*/
	  
        // $this->addColumn('action',
        //     array(
        //         'header'    =>  Mage::helper('quesanswer')->__('Action'),
        //         'width'     => '100',
        //         'type'      => 'action',
        //         'getter'    => 'getId',
        //         'actions'   => array(
        //             array(
        //                 'caption'   => Mage::helper('quesanswer')->__('Edit'),
        //                 'url'       => array('base'=> '*/*/edit'),
        //                 'field'     => 'id'
        //             )
        //         ),
        //         'filter'    => false,
        //         'sortable'  => false,
        //         'index'     => 'stores',
        //         'is_system' => true,
        // ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('quesanswer')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('quesanswer')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('quesanswer_id');
        $this->getMassactionBlock()->setFormFieldName('quesanswer');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('quesanswer')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('quesanswer')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('quesanswer/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('quesanswer')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('quesanswer')->__('Status'),
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