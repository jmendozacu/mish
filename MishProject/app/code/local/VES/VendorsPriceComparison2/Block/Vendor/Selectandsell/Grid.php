<?php
/**
 * Adminhtml customer grid block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsPriceComparison2_Block_Vendor_Selectandsell_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->_filterVisibility = false;
        $this->setId('selectAndSellGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
        $this->_emptyText = '<p style="font-size: 15px;">'.Mage::helper('pricecomparison2')->__('No records found. Do you want to <strong><a href="%s">add a new product</a></strong>?',$this->getUrl('vendors/catalog_product/new')).'</p>';
    
    }
    
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
	protected function _prepareCollection()
    {
        $vendorId = Mage::getSingleton('vendors/session')->getVendorId();
        $store = $this->_getStore();
        if(Mage::helper('catalog/product_flat')->isEnabled()) {
            $emulationModel = Mage::getModel('core/app_emulation');
            $init = $emulationModel->startEnvironmentEmulation(0, Mage_Core_Model_App_Area::AREA_ADMINHTML);
        }
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('vendor_id')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('vendor_sku')
            ->addAttributeToSelect('approval')
           // ->addAttributeToSelect('status')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('vendor_relation_key')
            ->addAttributeToSelect('vendor_parent_product')
            ->addAttributeToSelect('type_id');
        $original_collection = $collection;
        /*Do not display products from current vendor */
		$collection->addAttributeToFilter('approval','2')
		  ->addAttributeToFilter('type_id',array('in'=>array(
		      Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
		      Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
		      Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
		  )))
		  /*->addFieldToFilter('vendor_id',array('neq'=>$vendorId))*/
        ;
        if(Mage::helper('catalog/product_flat')->isEnabled()) {
            $emulationModel->stopEnvironmentEmulation($init);
        }

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
        $collection->addAttributeToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                    ->addAttributeToFilter('visibility',array('in'=>array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG)))
        ;

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    public function allowSell($product) {
        return Mage::helper('pricecomparison2')->allowToSell($product);
    }
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
       
        $renderer = new VES_VendorsProduct_Block_Widget_Grid_Column_Renderer_Image();
        $renderer->setVendor(Mage::getSingleton('vendors/session')->getVendor());
        $this->addColumnAfter('thumbnail',
            array(
                'header'=> Mage::helper('catalog')->__('Thumbnail'),
                'index' => 'entity_id',
                'type'  => 'text',
                'width'	=> '100px',
                'renderer'	=> $renderer,
                'filter'    => false,
                'sortable'  => false,
            ),'entity_id');
        
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
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
        
        $vendor = Mage::getSingleton('vendors/session')->getVendor();
        $vendorProductCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id',$vendor->getId());
        $sellingProductIds = $vendorProductCollection->getAllIds();
        $assignedProductCollection = Mage::getModel('pricecomparison2/pricecomparison')->getCollection()->addFieldToFilter('vendor_id',$vendor->getId());
        $assignedProductIds = $assignedProductCollection->getColumnValues('product_id');
        
        $sellingProductIds = array_merge($sellingProductIds, $assignedProductIds);
        
        $renderer = new VES_VendorsPriceComparison2_Block_Widget_Grid_Column_Renderer_Url();
        $renderer->setSellingProductIds($sellingProductIds);
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('pricecomparison2')->__('Action'),
                'width'     => '200',
                'renderer'  => $renderer,
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
                'align'     => 'center',
            ));
        parent::_prepareColumns();
        return $this;
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    public function getRowUrl($row)
    {
        return '';
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
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
