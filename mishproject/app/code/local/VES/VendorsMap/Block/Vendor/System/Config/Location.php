<?php
class VES_VendorsMap_Block_Vendor_System_Config_Location extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_defaultRenderer;
    protected $_actionRenderer;
    protected $_priceTypeRenderer;


    protected function _getColumnRenderer()
    {
        if (!$this->_defaultRenderer) {
           // $this->_defaultRenderer = $this->getLayout()->createBlock(
               // 'helpdesk/adminhtml_system_config_ticket_default', '',
               // array('is_render_to_js_template' => true)
           // );
           // $this->_defaultRenderer->setExtraParams('style="width:200px"');
        }
        return $this->_defaultRenderer;
    }
    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {

		 $this->addColumn('column', array(
            'label' => Mage::helper('vendorsmap')->__('Column'),
            'renderer' => $this->_getColumnRenderer(),
            'style' => 'width:200px',
        ));
        $this->addColumn('title', array(
            'label' => Mage::helper('vendorsmap')->__('Title'),
            'style' => 'width:100px',
        ));

		$this->addColumn('width', array(
            'label' => Mage::helper('vendorsmap')->__('Width(px)'),
            'style' => 'width:60px',
        ));

        $this->addColumn('sort_order', array(
            'label' => Mage::helper('vendorsmap')->__('Sort Order'),
            'style' => 'width:60px',
        ));
   
        $this->addColumn('align', array(
        		'label' => Mage::helper('vendorsmap')->__('Align'),
        		'style' => 'width:60px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('vendorsmap')->__('Add Option');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getColumnRenderer()->calcOptionHash($row->getData('column')),
            'selected="selected"'
        );
        //Zend_Debug::dump($row, '_prepareArrayRow', true);
    }
}