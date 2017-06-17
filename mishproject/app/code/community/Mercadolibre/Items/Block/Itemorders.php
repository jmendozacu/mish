<?php
class Mercadolibre_Items_Block_Itemorders extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getItems()     
     { 
        if (!$this->hasData('itemorders')) {
            $this->setData('itemorders', Mage::registry('itemorders'));
        }
        return $this->getData('itemorders');
        
    }
}