<?php
class OTTO_AdvancedFaq_Block_Seller_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'seller_category';
    $this->_blockGroup = 'advancedfaq';
    $this->_headerText = Mage::helper('advancedfaq')->__('Topic Manager');
    $this->_addButtonLabel = Mage::helper('advancedfaq')->__('Add Topic');
    parent::__construct();
  }
}

