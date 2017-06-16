<?php
/**
 * Adminhtml customer grid block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsPriceComparison2_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->_filterVisibility = true;
        $this->setId('assignedProductsGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(false);
        $this->setVarNameFilter('product_filter');
    
    }
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
	protected function _prepareCollection()
    {
        $collection = Mage::getModel('pricecomparison2/pricecomparison')->getCollection();
        $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'name');
		/*
        $collection->getSelect()->join(
            array('catalog_product_name' => $collection->getTable('catalog/product').'_varchar'),
            'catalog_product_name.entity_id=main_table.product_id AND attribute_id='.$attributeModel->getId(),
            array('product_name'=>'value')
        ); 
        */
        $collection->getSelect()->join(
            array('vendor_entity' => $collection->getTable('vendors/vendor')),
            'vendor_entity.entity_id=main_table.vendor_id',
            array('vendor_identifier'=>'vendor_entity.vendor_id')
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $cond = $column->getFilter()->getCondition();
                if ($field && isset($cond)) {
                    if($field == 'status'){
                        $this->getCollection()->addFieldToFilter('main_table.status' , $cond);
                    }elseif($field == 'condition'){
                        $this->getCollection()->addFieldToFilter('main_table.condition' , $cond);
                    }else{
                        $this->getCollection()->addFieldToFilter($field , $cond);
                    }
                }
            }
        }
        return $this;
    }
    public function allowSell($product) {
        return Mage::helper('pricecomparison2')->allowToSell($product);
    }
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumn('id',array(
            'header'=> Mage::helper('catalog')->__('ID'),
            'index' => 'entity_id',
            'width' => 100,
        ));
        $this->addColumn('vendor_id',array(
            'header'=> Mage::helper('catalog')->__('Vendor Id'),
            'index' => 'vendor_identifier',
            'width' => 150,
        ));
        $this->addColumn('name',array(
            'header'=> Mage::helper('catalog')->__('Name'),
            'index' => 'product_name',
            'width' => 250,
			'renderer' => new VES_VendorsPriceComparison2_Block_Widget_Grid_Column_Renderer_Name()
        ));
        $this->addColumn('description',array(
            'header'=> Mage::helper('catalog')->__('Short Description'),
            'index' => 'description',
        ));
        $store = $this->_getStore();
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

        parent::_prepareColumns();
        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('price_id');
        $this->getMassactionBlock()->setFormFieldName('pricecomparison');

        $statuses = Mage::getSingleton('pricecomparison2/source_status')->getOptionArray();

        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('vendors')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('vendors')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

    public function getRowUrl($item)
    {
        return '';
    }
//     public function getGridUrl()
//     {
//         return $this->getUrl('*/*/productListGrid', array('_current'=>true));
//     }
    
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
