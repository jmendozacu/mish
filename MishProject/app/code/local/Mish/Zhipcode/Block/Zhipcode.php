<?php
class Mish_Zhipcode_Block_Zhipcode extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getZhipcode()     
     { 
        if (!$this->hasData('zhipcode')) {
            $this->setData('zhipcode', Mage::registry('zhipcode'));
        }
        return $this->getData('zhipcode');
        
    }
}