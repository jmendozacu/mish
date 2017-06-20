<?php

class Mercadolibre_Items_Block_Feedbacks extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getFeedbacks() {
        if (!$this->hasData('feedbacks')) {
            $this->setData('feedbacks', Mage::registry('feedbacks'));
        }
        return $this->getData('feedbacks');
    }

}