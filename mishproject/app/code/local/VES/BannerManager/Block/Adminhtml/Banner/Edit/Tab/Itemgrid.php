<?php

class VES_BannerManager_Block_Adminhtml_Banner_Edit_Tab_Itemgrid extends Mage_Adminhtml_Block_Widget_Grid
{
public function __construct()
  {
      parent::__construct();
      $this->setId('itemGrid');
      $this->setDefaultSort('item_id');
      $this->setDefaultDir('ASC');
      $this->setUseAjax(true);
      $this->setSaveParametersInSession(true);
     // //Mage::helper('ves_core');
      $this->setTemplate('ves_bannermanager/grid.phtml');
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('bannermanager/item')->getCollection()->addFieldToFilter('banner_id',Mage::registry('current_banner')->getId());
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('item_id', array(
          'header'    => Mage::helper('bannermanager')->__('ID'),
          'align'     =>'center',
          'width'     => '50px',
          'index'     => 'item_id',
      ));
     ////Mage::helper('ves_core');
	  $this->addColumn('filename', array(
          'header'    => Mage::helper('bannermanager')->__('Image'),
          'align'     =>'left',
          'index'     => 'filename',
	  	  'width'	  => '100px',
      ));
      
      
      $this->addColumn('item_title', array(
          'header'    => Mage::helper('bannermanager')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('bannermanager')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('item_status', array(
          'header'    => Mage::helper('bannermanager')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('bannermanager')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('bannermanager')->__('Edit'),
                        'url'       => array('base'=> '*/adminhtml_item/edit'),
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
       
        return $this;
    }
	public function getGridUrl()
    {
        return $this->getUrl('*/*/Itemgrid', array('_current'=>true,'banner_id'=>Mage::registry('current_banner')->getId()));
    }
  public function getRowUrl($row)
  {
      return $this->getUrl('*/banner_item/edit', array('id' => $row->getId()));
  }

}