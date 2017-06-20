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
 * @package     Magestore_Inventorysupplyneeds
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysupplyneeds Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysupplyneeds
 * @author      Magestore Developer
 */
class VES_VendorsInventory_Block_Inventorysupplyneeds extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'inventorysupplyneeds';
        $this->_blockGroup = 'vendorsinventory';
        $this->_headerText = Mage::helper('vendorsinventory')->__('Generate Purchase Orders from Supply Needs');
        parent::__construct();
        $this->setTemplate('ves_vendorsinventory/supplyneeds/content-header.phtml');
        $this->_removeButton('add');
        $filter = $this->getRequest()->getParam('top_filter');
        $helperClass = Mage::helper('vendorsinventory/supplyneeds');
        if ($filter)
            $helperClass->setTopFilter($filter);
    }
    
    /**
     * 
     * @return \Magestore_Inventorysupplyneeds_Model_Draftpo
     */
    public function getDraftPO(){
        if(!$this->hasData('draft_purchaseorder')){
            $draftPO = $this->helper('vendorsinventory/supplyneeds')->getDraftPO();
            $this->setData('draft_purchaseorder', $draftPO);
        }
        return $this->getData('draft_purchaseorder');
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasDraftPO(){
        if($this->getDraftPO()->getId())
            return true;
        return false;
    }
    
    /**
     * 
     * @return string
     */
    public function getDraftPOUrl() {
        if(!$this->hasDraftPO())
            return null;
        return $this->getUrl('vendors/inventory_draftpo/view', array('id'=>$this->getDraftPO()->getId()));
    }    

}
