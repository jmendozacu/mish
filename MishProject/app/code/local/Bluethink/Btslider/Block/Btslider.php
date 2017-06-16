<?php
class Bluethink_Btslider_Block_Btslider extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBtslider()     
     { 
        if (!$this->hasData('btslider')) {
            $this->setData('btslider', Mage::registry('btslider'));
        }
        return $this->getData('btslider');
        
    }
}