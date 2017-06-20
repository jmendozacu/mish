<?php
class Mercadolibre_Items_Block_Anstemplate extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getQuestions()     
     { 
        if (!$this->hasData('anstemplate')) {
            $this->setData('anstemplate', Mage::registry('anstemplate'));
        }
        return $this->getData('anstemplate');
        
    }
}