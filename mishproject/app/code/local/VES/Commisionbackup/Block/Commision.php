<?php
class VES_Commision_Block_Commision extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCommision()     
     { 
        if (!$this->hasData('commision')) {
            $this->setData('commision', Mage::registry('commision'));
        }
        return $this->getData('commision');
        
    }
}