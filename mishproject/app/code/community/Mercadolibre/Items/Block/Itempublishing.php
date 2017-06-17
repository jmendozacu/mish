<?php
class Mercadolibre_Items_Block_Itempublishing extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getItempublishing()     
     { 
        if (!$this->hasData('itempublishing')) {
            $this->setData('itempublishing', Mage::registry('itempublishing'));
        }
        return $this->getData('itempublishing');
        
    }
}