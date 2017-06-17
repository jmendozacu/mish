<?php

class Code5_Supplierinventory_Block_Adminhtml_Supplierinventory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Init class
     */
    public function __construct() {
        $this->_blockGroup = 'supplierinventory';
        $this->_controller = 'adminhtml_supplierinventory';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Inventory'));
        $this->_updateButton('delete', 'label', $this->__('Delete Inventory'));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('custom')->getId()) {
            return $this->__('Edit Inventory');
        } else {
            return $this->__('New Inventory');
        }
    }

}
