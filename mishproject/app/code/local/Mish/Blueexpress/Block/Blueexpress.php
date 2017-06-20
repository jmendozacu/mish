<?php
class Mish_Blueexpress_Block_Blueexpress extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBlueexpress()     
     { 
        if (!$this->hasData('blueexpress')) {
            $this->setData('blueexpress', Mage::registry('blueexpress'));
        }
        return $this->getData('blueexpress');
        
    }
}