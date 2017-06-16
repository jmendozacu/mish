<?php
class Mirasvit_Seo_Block_Adminhtml_System_NoindexPages extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_optionsRenderer;

    protected function _getOptionsRenderer()
    {
        if (!$this->_optionsRenderer) {
            $this->_optionsRenderer = Mage::app()->getLayout()->createBlock(
                'seo/adminhtml_system_noindexOption', '',
                array('is_render_to_js_template' => true)
            );
            $this->_optionsRenderer->setClass('customer_options_select');
            $this->_optionsRenderer->setExtraParams('style="width:150px"');
        }
        return $this->_optionsRenderer;
    }

    public function __construct()
    {
        $this->addColumn('pattern', array(
            'label' => Mage::helper('seo')->__('URL Pattern'),
            'style' => 'width:250px',
        ));
        $select = $this->_getOptionsRenderer();

        $this->addColumn('option', array(
            'label' => Mage::helper('seo')->__('Option'),
            'renderer' => $select,
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add');
        parent::__construct();
    }

    /************************/


    public function getArrayRows()
    {
        $result = parent::getArrayRows();

        foreach ($result as $key => $row) {
            $this->prepareArrayRow($row);
        }
        return $result;
    }

    protected function prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getOptionsRenderer()->calcOptionHash($row->getData('option')),
            'selected="selected"'
        );
    }
}