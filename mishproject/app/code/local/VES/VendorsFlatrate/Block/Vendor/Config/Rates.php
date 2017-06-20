<?php
class VES_VendorsFlatrate_Block_Vendor_Config_Rates extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_defaultRenderer;

	/**
     * Check if columns are defined, set template
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendorsflatrate/system/config/form/field/array.phtml');
    }
    
    protected function _getTypeRenderer()
    {
        if (!$this->_defaultRenderer) {
            $this->_defaultRenderer = $this->getLayout()->createBlock(
                'vendorsflatrate/vendor_config_rates_type', '',
                array('is_render_to_js_template' => true)
            );
            $this->_defaultRenderer->setExtraParams('style="width:100px"');
        }
        return $this->_defaultRenderer;
    }
    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
    	$this->addColumn('identifier', array(
            'label'		=> Mage::helper('vendorsshipping')->__('Identifier'),
            'style' 	=> 'width:100px',
			'required'	=> true,
			'class'		=> 'input-text validate-alphanum required-entry shipping-validate-unique',
        ));
		$this->addColumn('title', array(
            'label'		=> Mage::helper('vendorsshipping')->__('Title'),
            'style' 	=> 'width:150px',
			'required'	=> true,
			'class'		=> 'input-text required-entry',
        ));
		
        $this->addColumn('type', array(
            'label' => Mage::helper('vendorsshipping')->__('Type'),
            'renderer' => $this->_getTypeRenderer(),
            'style' => 'width:50px',
        ));
        

		$this->addColumn('price', array(
            'label' 	=> Mage::helper('vendorsshipping')->__('Price'),
			'style' 	=> 'width:50px',
			'class'		=> 'input-text required-entry validate-number',
			'required'	=> true,
        ));
        
        
        $this->addColumn('free_shipping_subtotal', array(
            'label' => '<span style="text-decoration: underline;cursor: help;" onmousemove="$(\'vendor_shipping_free_shipping_subtotal\').show();" onmouseout="$(\'vendor_shipping_free_shipping_subtotal\').hide();">'.Mage::helper('vendorsshipping')->__('Free Shipping').'</span><div id="vendor_shipping_free_shipping_subtotal" style="background: #fff;border: 1px solid #d3d3d3;border-radius: 5px;display: none;margin-top: -55px;padding: 5px 10px;position: absolute;">'.Mage::helper('vendorsshipping')->__('Minimum Order Amount for Free Shipping').'</div>',
        	'style' => 'width:50px',
        	'class'	=> 'input-text validate-number',
        ));
   		
		$this->addColumn('sort_order', array(
            'label' => Mage::helper('vendorsshipping')->__('Sort Order'),
			'style' => 'width:50px',
			'class'	=> 'input-text validate-number',
        ));
       
        
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('vendorsshipping')->__('Add Rate');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getTypeRenderer()->calcOptionHash($row->getData('type')),
            'selected="selected"'
        );
    }
}