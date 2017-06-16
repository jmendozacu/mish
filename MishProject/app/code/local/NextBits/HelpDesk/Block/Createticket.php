<?php   
class NextBits_HelpDesk_Block_Createticket extends Mage_Core_Block_Template{   

	public function __construct()
    {
        parent::__construct();
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
       $collection = Mage::getModel('helpdesk/ticket')->getCollection()->addFieldToFilter('customer_id', $customerId);
        $this->setCollection($collection);
    }
	 
	public function _prepareLayout(){	
		parent::_prepareLayout();		
		$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
		$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
		$pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
	} 
	
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }



}