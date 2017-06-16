<?php
class VES_VendorsFlatrate_Block_Vendor_Config_Rates_Type extends Mage_Core_Block_Html_Select
{

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption('O', $this->__('Per Order'));
			$this->addOption('I', $this->__('Per Item'));
        }
        return parent::_toHtml();
    }
}
