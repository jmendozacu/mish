<?php
class Mish_Shipit_Block_Shipit extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getShipit()     
     { 
        if (!$this->hasData('shipit')) {
            $this->setData('shipit', Mage::registry('shipit'));
        }
        return $this->getData('shipit');
        
    }
}