<?php

class VES_VendorsLiveChat_Model_Message extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorslivechat/message');
    }

    public function converMessageVendor($message,$time){
        $html = '<li class="other" id="ves-content-message-'.$message->getId().'">';
        $html .= '<div class="avatar">';
        $html .= '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."ves_vendors/livechat/avatar.jpg".'" />';
        $html .= '</div>';
        $html .= '<div class="messages">';
        $html .= '<p>'.$message.'</p>';
        $html .= '<time datetime="'.$time.'">'.Mage::getModel('core/date')->date("F j, Y, g:i a",$time ).'</p>';
        $html .= '</div>';
        $html .= '</li>';
        return $html;
    }
    public function converMessage($message,$time){
        $html = '<li class="other" id="ves-content-message-'.$message->getId().'">';
        $html .= '<div class="avatar">';
        $html .= '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."ves_vendors/livechat/avatar.jpg".'" />';
        $html .= '</div>';
        $html .= '<div class="messages">';
        $html .= '<p>'.$message.'</p>';
        $html .= '<time datetime="'.$time.'">'.Mage::getModel('core/date')->date("F j, Y, g:i a",$time ).'</p>';
        $html .= '</div>';
        $html .= '</li>';
        return $html;
    }


}