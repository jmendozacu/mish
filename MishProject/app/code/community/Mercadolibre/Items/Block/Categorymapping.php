<?php
class Mercadolibre_Items_Block_Categorymapping extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCategorymapping()     
     { 
        if (!$this->hasData('mapcategoriesdata')) {
            $this->setData('mapcategoriesdata', Mage::registry('mapcategoriesdata'));
        }
        return $this->getData('mapcategoriesdata');
        
    }
}