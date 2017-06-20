<?php

/**
 * 
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <core@magentocommerce.com>
 */
class VES_VendorsRma_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Template
{
    protected $_collection;
    /**
     * Get pending product collection
     */
	public function getRequestCollection(){
	    if(!$this->_collection){
    	    $this->_collection = Mage::getModel('vendorsrma/request')->getCollection()
    	    ->addAttributeToFilter('state',VES_VendorsRma_Model_Option_State::STATE_BEING);
	    }
	    
	    return $this->_collection;
	}
	/**
	 * Get number of pending product
	 */
	public function getStateBeingCount(){
	    return $this->getRequestCollection()->count();
	}
	
	
	public function getMessage(){
	    $requestCount = $this->getStateBeingCount();
	    return $this->__('There are %s requests are awaiting for your review. <a href="%s">Click Here</a> to review them.','<strong>'.$requestCount.'</strong>',$this->getUrl('*/rma_request/index'));
	}
	public function _toHtml(){
	    if(!$this->getStateBeingCount()) return '';
	    return parent::_toHtml();
	}
}
