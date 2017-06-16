<?php
class Mercadolibre_Items_Block_Questions extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getQuestions()     
     { 
        if (!$this->hasData('questions')) {
            $this->setData('questions', Mage::registry('questions'));
        }
        return $this->getData('questions');
        
    }
}