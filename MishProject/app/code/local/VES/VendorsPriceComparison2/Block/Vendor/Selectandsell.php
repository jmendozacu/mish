<?php

/**
 * Catalog manage products block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsPriceComparison2_Block_Vendor_Selectandsell extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Set template
     */
    public function __construct()
    {
        $this->_controller = 'vendor_selectandsell';
        $this->_blockGroup = 'pricecomparison2';
        $this->_headerText = Mage::helper('pricecomparison2')->__('Select and Sell');

        parent::__construct();
        $this->setTemplate('ves_vendorspricecomparison2/widget/grid/container.phtml');
        $this->_removeButton('add');
        $this->_addButton('back', array(
            'label'     => $this->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() .'\')',
            'class'     => 'back',
        ));
    }
	/**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
        $this->setChild( 'search_form',
            $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_search',
                $this->_controller . '.search'));
        
		return parent::_prepareLayout();
    }
    
    /**
     * Get Search Html
     * @return Ambigous <string, multitype:>
     */
    public function getSearchHtml(){
        return $this->getChildHtml('search_form');
    }
    
    /**
     * Get Back URL
     * @return string $url
     */
    public function getBackUrl(){
        return $this->getUrl('*/*');
    }
}
