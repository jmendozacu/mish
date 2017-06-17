<?php
class Mirasvit_Kb_Block_Adminhtml_Article_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
        $this->setDefaultSort('article_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('kb/article')
            ->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn('article_id', array(
            'header'    => Mage::helper('kb')->__('ID'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'article_id',
            )
        );
        $this->addColumn('name', array(
            'header'    => Mage::helper('kb')->__('Title'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'name',
            )
        );
        $this->addColumn('is_active', array(
            'header'    => Mage::helper('kb')->__('Active'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => $this->__('No'),
                1 => $this->__('Yes')
            ),
            )
        );
        $this->addColumn('user_id', array(
            'header'    => Mage::helper('kb')->__('Author'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'user_id',
            'type'      => 'options',
            'options'   => Mage::helper('kb')->getAdminUserOptionArray(),
            )
        );
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('kb')->__('Created At'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'created_at',
            'type'      => 'date',
            )
        );
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('kb')->__('Updated At'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'updated_at',
            'type'      => 'date',
            )
        );
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('article_id');
        $this->getMassactionBlock()->setFormFieldName('article_id');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('kb')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('kb')->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /************************/

}