<?php
class OTTO_AdvancedFaq_Block_Seller_Faq extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'seller_faq';
    $this->_blockGroup = 'advancedfaq';
    $this->_headerText = Mage::helper('advancedfaq')->__('Faq Manager');
    $this->_addButtonLabel = Mage::helper('advancedfaq')->__('Add Faq');
    parent::__construct();
  }
}
