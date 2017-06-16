<?php
class VES_VendorsSelectAndSell_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_namePrefix = 'product';
    /**
     * get allow attribute and position tab
     * @param $tab string
     * @return array
     */
    public function getAllowAttributeForSell($tab = null) {
        $resource = Mage::getConfig()->getNode('vendors/sell/attributes')->asArray();
        $allow = array();
        foreach($resource as $code => $info) {
            $allow[$code] = $info;
            if($info['tab'] == $tab) return $info;
        }
        return $allow;
    }

    public function getAllowTabForSell() {
        $resource = Mage::getConfig()->getNode('vendors/sell/tabs')->asArray();
        $allow = array();
        foreach($resource as $code=>$info) {
            $allow[$code] = $info;
        }
        return $allow;
    }

    /**
     * check product allow to sell
     * @param $product
     * @return bool
     */
    public function allowToSell($product) {
        if($product->getVendorId() == Mage::getSingleton('vendors/session')->getVendor()->getId()) return false;
        if($key = $product->getData('vendor_relation_key')) {
			if(Mage::helper('catalog/product_flat')->isEnabled()) {
				$emulationModel = Mage::getModel('core/app_emulation');
				$init = $emulationModel->startEnvironmentEmulation(0, Mage_Core_Model_App_Area::AREA_ADMINHTML);
			}
            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('vendor_id',Mage::getSingleton('vendors/session')->getVendor()->getId())
                ->addAttributeToFilter('vendor_relation_key', $product->getData('vendor_relation_key'))
                ->addFieldToFilter('entity_id',array('neq'=>$product->getId()))
            ;
			
			if(Mage::helper('catalog/product_flat')->isEnabled()) {
				$emulationModel->stopEnvironmentEmulation($init);
			}

            if($collection->count()) return false;
            return true;
        }
        return true;
    }

    /**
     * check product sold by another product (is child)
     * @param $product
     * @return bool
     */
    public function isSoldProduct($product) {
        if($product->getData('vendor_child_product') == '1') return true;
        return false;
    }

    /*
     * check is sell controller
     * @return bool
     */
    public function isSellController() {
        if(Mage::registry('is_sell_pre_dispatch') == '1') return true;
        return false;
    }

    public function getPrefixName() {
        return $this->_namePrefix;
    }

    public function defaultInventoryWithoutStock() {
        return array(
            'qty'                               =>  '0',
            'is_in_stock'                       =>  '0',
            'use_config_manage_stock'           =>  '1',
            'original_inventory_qty'            =>  '1',
            'use_config_min_qty'                =>  '1',
            'use_config_min_sale_qty'           =>  '1',
            'use_config_max_sale_qty'           =>  '1',
            'is_qty_decimal'                    =>  '1',
            'is_decimal_divided'                =>  '1',
            'use_config_backorders'             =>  '1',
            'use_config_notify_stock_qty'       =>  '1',
            'use_config_enable_qty_increments'  =>  '1',
            'use_config_qty_increments'         =>  '1',
        );
    }
}