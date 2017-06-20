<?php

class VES_BannerManager_Block_Vendor_Banner_Edit_Tab_Itemgrid extends VES_BannerManager_Block_Adminhtml_Banner_Edit_Tab_Itemgrid
{
  protected function _prepareColumns()
  {
      parent::_prepareColumns();
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('bannermanager')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('bannermanager')->__('Edit'),
                        'url'       => array('base'=> '*/cms_banner_item/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
	  
      return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
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