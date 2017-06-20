<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Supplier Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventoryphysicalstocktaking
 * @author      Magestore Developer
 */
class VES_VendorsInventory_Block_Physicalstocktaking extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'physicalstocktaking';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('vendorsinventory')->__('Physical Stocktaking');
        $this->_addButtonLabel = Mage::helper('vendorsinventory')->__('Create New Physical Stocktaking');
        parent::__construct();
        $this->setTemplate('ves_vendorsinventory/physicalstocktaking/new.phtml');
    }
    
    public function getWarehouseByAdmin(){ //echo 'VES_VendorsInventory_Block_Physicalstocktaking';die;
        $adminId = Mage::getSingleton('vendors/session')->getUser()->getId();
        $warehouseIds = array();
        $collection = Mage::getModel('vendorsinventory/permission')->getCollection()
                            ->addFieldToFilter('vendor_id',$adminId)
                            ->addFieldToFilter('can_physical',1);
        foreach($collection as $assignment){
            $warehouseIds[] = $assignment->getWarehouseId();
        }
        $warehouseCollection = Mage::getModel('inventoryplus/warehouse')->getCollection()
                                    ->addFieldToFilter('warehouse_id',array('in'=>$warehouseIds));
        return $warehouseCollection;
    }
}