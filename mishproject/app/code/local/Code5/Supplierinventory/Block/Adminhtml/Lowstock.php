<?php

class Code5_Supplierinventory_Block_Adminhtml_Lowstock extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() { 
        $this->_blockGroup = 'supplierinventory';
        $this->_controller = 'adminhtml_lowstock';
        $this->_headerText = $this->__('Manage Lowstock Inventory');

        parent::__construct();
    }

}
