<?php
class Mirasvit_Rma_Block_Adminhtml_Rma_Edit_Form_History extends Mirasvit_Rma_Block_Adminhtml_Rma_Edit_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mst_rma/rma/edit/form/history.phtml');
    }
}