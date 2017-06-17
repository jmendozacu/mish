<?php

class Code5_Supplierinventory_Block_Adminhtml_Supplierinventory extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_blockGroup = 'supplierinventory';
        $this->_controller = 'adminhtml_Supplierinventory';
        $this->_headerText = $this->__('Manage Suppliers Inventories');

        parent::__construct();
    }

}
