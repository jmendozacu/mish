<?php
/**
 *  edit block
 *
 * @category   VES
 * @package    VES_VendorsPriceComparison2
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsPriceComparison2_Block_Vendor_Selectandsell_Search extends Mage_Adminhtml_Block_Template
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ves_vendorspricecomparison2/search.phtml');
    }

    /**
     * Get Grid Js Object Name
     * @return string
     */
    public function getGridJsObjectName(){
        return $this->getParentBlock()->getChild('grid')->getJsObjectName();
    }
    
    /**
     * Get Session Name Param
     * @return string
     */
    public function getSessionNameParam(){
        $param = $this->getRequest()->getParam('product_filter');
        if(!$param){
            $gridId = $this->getParentBlock()->getChild('grid')->getId();
            $sessionParamName = $gridId.'product_filter';
            $param = base64_decode(Mage::getSingleton('adminhtml/session')->getData($sessionParamName));
            parse_str($param,$filter);
        }
        return isset($filter['name'])?($filter['name']!=VES_VendorsPriceComparison2_Helper_Data::SPECIAL_CHAR?$filter['name']:''):'';
    }
}
