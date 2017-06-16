<?php
class VES_VendorsRma_Model_System_Config_Source_Notify
{
    const CONFIG_ALL = 'all';
    const CONFIG_ADMIN = 'admin';
    const CONFIG_CUSTOMER = 'customer';
    const CONFIG_VENDOR = 'vendor';
    const CONFIG_DISABLE = 'disable';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::CONFIG_ALL, 'label'=>Mage::helper('vendorsrma')->__('All')),
            array('value' => self::CONFIG_ADMIN, 'label'=>Mage::helper('vendorsrma')->__('Admin Only')),
            array('value' => self::CONFIG_CUSTOMER, 'label'=>Mage::helper('vendorsrma')->__('Customer Only')),
            array('value' => self::CONFIG_VENDOR, 'label'=>Mage::helper('vendorsrma')->__('Vendor Only')),
            array('value' => self::CONFIG_DISABLE, 'label'=>Mage::helper('vendorsrma')->__('Disable')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            self::CONFIG_ALL => Mage::helper('vendorsrma')->__('All'),
            self::CONFIG_ADMIN => Mage::helper('vendorsrma')->__('Admin Only'),
            self::CONFIG_CUSTOMER => Mage::helper('vendorsrma')->__('Customer Only'),
            self::CONFIG_VENDOR => Mage::helper('vendorsrma')->__('Vendor Only'),
            self::CONFIG_DISABLE => Mage::helper('vendorsrma')->__('Disable'),
        );
    }

}
