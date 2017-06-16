<?php
/**
 * Adminhtml customer grid block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSelectAndSell_Block_Vendor_Product_Grid extends VES_VendorsProduct_Block_Vendor_Product_Grid
{

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
        /*Filter by Vendor Id */
		$collection->addFieldToFilter('vendor_id',array('neq'=>$vendorId))
                    //->addFieldToFilter('vendor_id',array('notnull'=>true))
                    //->addFieldToFilter('vendor_id',array('gt'=>'0'))
                    ->addAttributeToFilter('approval','2')
                    ->addAttributeToFilter('type_id', array('eq' => 'simple'))
                    ->addAttributeToFilter('vendor_parent_product', array('eq' => 0))

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

        $not_allow_ids = array();
        foreach($collection as $key => $product) {
            if(!$this->allowSell($product)) $not_allow_ids[] = $product->getId();
        }
       // var_dump($not_allow_ids);
        if(count($not_allow_ids)) $collection->getSelect()->where('e.entity_id not in(?)', $not_allow_ids);

      //  echo $collection->getSelect();
        $collection->clear();
        //$collection->reset();
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    public function allowSell($product) {
        return Mage::helper('selectandsell')->allowToSell($product);
    }
    
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->removeColumn('type')->removeColumn('set_name')->removeColumn('approval')
        ->removeColumn('vendor_sku')->removeColumn('status')->removeColumn('visibility');

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('selectandsell')->__('Action'),
                'width'     => '60',
                'renderer'  => 'VES_VendorsSelectAndSell_Block_Widget_Grid_Column_Renderer_Url',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));
    }

    protected function _prepareMassaction()
    {
//        $this->setMassactionIdField('entity_id');
//        $this->getMassactionBlock()->setFormFieldName('vendorsnews');
//
//        $this->getMassactionBlock()->addItem('sell', array(
//            'label'    => Mage::helper('selectandsell')->__('Sell'),
//            'url'      => $this->getUrl('*/*/sell'),
//            'confirm'  => Mage::helper('selectandsell')->__('Are you sure?')
//        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return '';
    }
}
