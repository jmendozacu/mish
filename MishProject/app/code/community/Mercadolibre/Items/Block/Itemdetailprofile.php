<?php
class Mercadolibre_Items_Block_Itemdetailprofile extends Mage_Core_Block_Template
{
	
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function getItemdetailprofile()     
     { 
        if (!$this->hasData('itemdetailprofile')) {
            $this->setData('itemdetailprofile', Mage::registry('itemdetailprofile'));
        }
        return $this->getData('itemdetailprofile');
    }
}