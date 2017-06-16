<?php
class Mercadolibre_Items_Block_Mastertemplates extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getMastertemplates()     
     { 
        if (!$this->hasData('mastertemplates')) {
            $this->setData('mastertemplates', Mage::registry('mastertemplates'));
        }
        return $this->getData('mastertemplates');
        
    }
}