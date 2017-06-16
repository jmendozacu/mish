<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alesioo
 * Date: 15.04.13
 * Time: 18:10
 * To change this template use File | Settings | File Templates.
 */ 
class Etheme_Ajaxaddto_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer {
    public function getDeleteUrl()
    {
        if ($this->hasDeleteUrl()) {
            return $this->getData('delete_url');
        }

        return $this->getUrl(
            'ajax/index/delete',
            array(
                'id'=>$this->getItem()->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->helper('core/url')->getEncodedUrl()
            )
        );
    }
}