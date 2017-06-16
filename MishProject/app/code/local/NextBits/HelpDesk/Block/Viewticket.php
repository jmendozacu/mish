<?php   
class NextBits_HelpDesk_Block_Viewticket extends Mage_Core_Block_Template{   
	
	public function getBackUrl()
    {
        /* if ($this->getData('back_url')) {
            return $this->getData('back_url');
        } */

       return $this->getUrl('helpdesk/index/');
        
    }
}