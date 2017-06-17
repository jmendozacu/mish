<?php
class VES_VendorsLiveChat_Block_Color extends Mage_Core_Block_Template
{

    public function getColor($url){
        $color = Mage::getStoreConfig($url);
        return $color;
    }
}