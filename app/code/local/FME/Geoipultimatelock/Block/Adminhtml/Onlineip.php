<?php

class FME_Geoipultimatelock_Block_Adminhtml_Onlineip extends Mage_Adminhtml_Block_Widget_Grid_Container {
    
    public function __construct() {
        $this->_controller = 'adminhtml_onlineip';
        $this->_blockGroup = 'geoipultimatelock';
        $this->_headerText = Mage::helper('geoipultimatelock')->__('Online IP Manager');
        $this->_addButtonLabel = Mage::helper('geoipultimatelock')->__('Add ACL');
        parent::__construct();
        $this->removeButton('add');
    }
    
    public function getHeaderCssClass() {
        return 'icon-head head-products';
    }
}

