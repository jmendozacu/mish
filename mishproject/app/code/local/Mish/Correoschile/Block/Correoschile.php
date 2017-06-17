<?php
class Mish_Correoschile_Block_Correoschile extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCorreoschile()     
     { 
        if (!$this->hasData('correoschile')) {
            $this->setData('correoschile', Mage::registry('correoschile'));
        }
        return $this->getData('correoschile');
        
    }
}