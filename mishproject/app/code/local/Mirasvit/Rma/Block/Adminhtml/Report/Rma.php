<?php

class Mirasvit_Rma_Block_Adminhtml_Report_Rma extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_report_rma';
        $this->_blockGroup = 'rma';
        $this->_headerText = Mage::helper('reports')->__('RMAs Report');
        parent::__construct();
        $this->setTemplate('report/grid/container.phtml');
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/index', array('_current' => true));
    }

    /************************/

}