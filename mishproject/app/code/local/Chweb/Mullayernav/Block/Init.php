<?php
class Chweb_Mullayernav_Block_Init extends Mage_Core_Block_Template
{
    public function isActive()
    {
        return Mage::getStoreConfig('mullayernav/general/enabled');
    }
    
    public function shouldLoad()
    {
        $toolbar = $this->getLayout()->getBlock('product_list_toolbar');
        if (!empty($toolbar) && $toolbar->getCollection() && $toolbar->getLastPageNum() > 1):
            return true;
        endif;
        
        return false;
    }
    
    public function getLastPage()
    {
        $toolbar = $this->getLayout()->getBlock('product_list_toolbar');
        
        return !empty($toolbar) && $toolbar->getCollection() ? $toolbar->getLastPageNum() : 1;
    }
    
    public function getMode()
    {
        $toolbar = $this->getLayout()->getBlock('product_list_toolbar');
        
        return !empty($toolbar) ? $toolbar->getCurrentMode() : false;
    }
}
