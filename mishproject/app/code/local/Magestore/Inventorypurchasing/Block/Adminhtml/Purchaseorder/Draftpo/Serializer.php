<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventorypurchasing
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
class Magestore_Inventorypurchasing_Block_Adminhtml_Purchaseorder_Draftpo_Serializer
    extends Mage_Adminhtml_Block_Widget_Grid_Serializer{
    
    /**
     * Set serializer template
     *
     * @return Mage_Adminhtml_Block_Widget_Grid_Serializer
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('inventorypurchasing/purchaseorder/draftpo/grid/serializer.phtml');
        return $this;
    }  
    
    /**
     * Get update draff purchase order url
     * 
     * @return string
     */
    public function getUpdatePOUrl(){
        return Mage::getModel('adminhtml/url')->getUrl('*/*/update', array('id'=>$this->getRequest()->getParam('id'), '_secure' => true));
    }
    
}
