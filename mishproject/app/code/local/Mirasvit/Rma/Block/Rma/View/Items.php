<?php
class Mirasvit_Rma_Block_Rma_View_Items extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setData('area','frontend');
        $this->setTemplate('mst_rma/rma/email/items.phtml');
    }
}