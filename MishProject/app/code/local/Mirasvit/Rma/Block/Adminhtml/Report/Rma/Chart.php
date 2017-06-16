<?php
class Mirasvit_Rma_Block_Adminhtml_Report_Rma_Chart extends Mage_Core_Block_Template
{
    public function getCollection()
    {
        return $this->getGrid()->getCollection();
    }

    public function isShowChart()
    {
        $collection = $this->getCollection();
        if ($this->getCollection()->count() > 1 && $this->getFilterData()->getReportType() == 'all'){
            return true;
        }
        return false;
    }

    /************************/

}