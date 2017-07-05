<?php
class Bluethink_Recommendation_Block_Recommendation extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getRecommendation()     
     { 
        if (!$this->hasData('recommendation')) {
            $this->setData('recommendation', Mage::registry('recommendation'));
        }
        return $this->getData('recommendation');
        
    }
}