<?php
class Mercadolibre_Items_Block_Itemlisting extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getItemlisting()     
     { 
        if (!$this->hasData('itemlisting')) {
            $this->setData('itemlisting', Mage::registry('itemlisting'));
        }
        return $this->getData('itemlisting');
        
    }
}