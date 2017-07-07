<?php
class Bluethink_Quesanswer_Block_Quesanswer extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getQuesanswer()     
     { 
        if (!$this->hasData('quesanswer')) {
            $this->setData('quesanswer', Mage::registry('quesanswer'));
        }
        return $this->getData('quesanswer');
        
    }
}