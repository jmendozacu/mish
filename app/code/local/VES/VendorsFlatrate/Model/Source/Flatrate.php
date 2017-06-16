<?php
/**
 * Class VES_VendorsShipping_Model_Source_Flatrate
 */
class VES_VendorsFlatrate_Model_Source_Flatrate
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'O', 'label'=>Mage::helper('vendorsshipping')->__('Per Order')),
            array('value'=>'I', 'label'=>Mage::helper('vendorsshipping')->__('Per Item')),
        );
    }
}
