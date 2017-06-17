<?php
class Mercadolibre_Items_Block_Attributemapping extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getAttributemapping()     
     { 
        if (!$this->hasData('attributemapping')) {
            $this->setData('attributemapping', Mage::registry('attributemapping'));
        }
        return $this->getData('attributemapping');
        
    }
}