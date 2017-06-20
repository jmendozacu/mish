<?php
/**
 * Adminhtml customer grid block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsPriceComparison2_Block_Vendor_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->_filterVisibility = true;
        $this->setId('assignedProductsGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
    
    }
    
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
	protected function _prepareCollection()
    {
        $vendor = Mage::getSingleton('vendors/session')->getVendor();
         $collection = Mage::getModel('pricecomparison2/pricecomparison')->getCollection()->addFieldToFilter('vendor_id',$vendor->getId());
		 /*
        $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'name');
        $collection->getSelect()->join(
            array('catalog_product_name' => $collection->getTable('catalog/product').'_varchar'),
            'catalog_product_name.entity_id=main_table.product_id AND attribute_id='.$attributeModel->getId(),
            array('product_name'=>'value')
        );

        $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('name');
		
        $collection->joinTable(
            array('pricecomparison'=>'pricecomparison2/pricecomparison2'),
            'product_id=entity_id',
            array(
                'pricecomparison_id'=>'entity_id',
                'pricecomparison_price'=>'price',
                'pricecomparison_qty'=>'qty',
                'pricecomparison_condition'=>'condition',
                'pricecomparison_status'=>'status'
            ),
            'pricecomparison.vendor_id='.$vendor->getId()
        );
		 */
		//$collection->getSelect()->group('entity_id'); 
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function allowSell($product) {
        return Mage::helper('pricecomparison2')->allowToSell($product);
    }
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $store = $this->_getStore();
        $renderer = new VES_VendorsProduct_Block_Widget_Grid_Column_Renderer_Image();
        $renderer->setVendor(Mage::getSingleton('vendors/session')->getVendor());
        $this->addColumn('thumbnail',array(
            'header'=> Mage::helper('catalog')->__('Thumbnail'),
            'index' => 'entity_id',
            'type'  => 'text',
            'width'	=> '100px',
            'renderer'	=> $renderer,
            'filter'    => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('name',array(
            'header'=> Mage::helper('catalog')->__('Name'),
           // 'index' => 'product_name',
			'renderer' => new VES_VendorsPriceComparison2_Block_Widget_Grid_Column_Renderer_Name()
        ));
		
		
        if(Mage::helper('pricecomparison2')->isEnabledCondition()){
            $this->addColumn('condition',array(
                'header'=> Mage::helper('catalog')->__('Condition'),
                'index' => 'condition',
                'type'  => 'options',
                'options' => Mage::getModel('pricecomparison2/source_condition')->getOptionArray(),
                'width' => 80,
            ));
        }
        $this->addColumn('price',array(
            'header'=> Mage::helper('catalog')->__('Price'),
            'index' => 'price',
            'type'  => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'width' => 120,
        ));
        $this->addColumn('qty',array(
            'header'=> Mage::helper('catalog')->__('Qty'),
            'index' => 'qty',
            'width' => 120,
            'type'  => 'number',
        ));
        $this->addColumn('status',array(
            'header'=> Mage::helper('catalog')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'options' => Mage::getModel('pricecomparison2/source_status')->getOptionArray(),
            'width' => 80,
        ));
        $renderer = new VES_VendorsPriceComparison2_Block_Widget_Grid_Column_Renderer_Preview();
        $this->addColumn('preview',
            array(
                'header'    =>  Mage::helper('pricecomparison2')->__('Preview'),
                'width'     => '100',
                'renderer'  => $renderer,
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'name',
                'is_system' => true,
                'align'     => 'center',
            )
        );
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));
        parent::_prepareColumns();
        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('pricecomparison');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('vendors')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('vendors')->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($item)
    {
        return $this->getUrl('*/*/edit',array('id'=>$item->getId()));
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productListGrid', array('_current'=>true));
    }
    
    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml();
        
    }
}
