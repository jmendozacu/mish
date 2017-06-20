<?php

class Code5_Supplierinventory_Block_Adminhtml_Unchangedinventory extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() { echo 'test';die;
        $this->_blockGroup = 'unchangedinventory';
        $this->_controller = 'adminhtml_unchangedinventory';
        $this->_headerText = $this->__('Manage Unchanged Inventories');
        parent::__construct();
    }

}
