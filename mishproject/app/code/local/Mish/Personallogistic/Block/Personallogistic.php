<?php
class Mish_Personallogistic_Block_Personallogistic extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPersonallogistic()     
     { 
        if (!$this->hasData('personallogistic')) {
            $this->setData('personallogistic', Mage::registry('personallogistic'));
        }
        return $this->getData('personallogistic');
        
    }
}