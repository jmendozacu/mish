<?php
class Mercadolibre_Items_Block_Shippingprofile extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getShippingprofiles()     
     { 
        if (!$this->hasData('shippingprofiles')) {
            $this->setData('shippingprofiles', Mage::registry('shippingprofiles'));
        }
        return $this->getData('shippingprofiles');
    }
}