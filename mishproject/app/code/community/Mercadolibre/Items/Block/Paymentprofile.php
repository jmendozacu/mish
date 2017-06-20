<?php
class Mercadolibre_Items_Block_Paymentprofile extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getShippingprofile()     
     { 
        if (!$this->hasData('paymentprofile')) {
            $this->setData('paymentprofile', Mage::registry('paymentprofile'));
        }
        return $this->getData('paymentprofile');
        
    }
}