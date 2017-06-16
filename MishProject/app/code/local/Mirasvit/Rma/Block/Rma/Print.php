<?php

class Mirasvit_Rma_Block_Rma_Print extends Mage_Core_Block_Template
{
    public function getRma()
    {
        return Mage::registry('current_rma');
    }

}