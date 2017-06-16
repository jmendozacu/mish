<?php
class Mercadolibre_Items_Block_Itemtemplates extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getItemtemplates()     
     { 
        if (!$this->hasData('itemtemplates')) {
            $this->setData('itemtemplates', Mage::registry('itemtemplates'));
        }
        return $this->getData('itemtemplates');
        
    }
}