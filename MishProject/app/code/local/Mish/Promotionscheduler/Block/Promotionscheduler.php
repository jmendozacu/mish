<?php
class Mish_Promotionscheduler_Block_Promotionscheduler extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPromotionscheduler()     
     { 
        if (!$this->hasData('promotionscheduler')) {
            $this->setData('promotionscheduler', Mage::registry('promotionscheduler'));
        }
        return $this->getData('promotionscheduler');
        
    }
}