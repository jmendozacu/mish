<?php

class VES_VendorsRma_Block_Vendor_Request_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('rmarequest_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('vendorsrma')->__('RMA Information'));
        $request = Mage::registry("current_request");
        if($request->getId()){
            $this->setTemplate('widget/tabshoriz.phtml');
        }
    }

    protected function _beforeToHtml()
    {
        $request = Mage::registry("current_request");

        if($request->getData("order_incremental_id")){
            if(!$request->getId()) {
                $this->addTab('information', array(
                    'label' => Mage::helper('vendorsrma')->__('Request Information'),
                    'title' => Mage::helper('vendorsrma')->__('Request Information'),
                    'content' => $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tab_main')->toHtml(),
                ));
            }
            else{
                $this->addTab('message', array(
                    'label'     => Mage::helper('vendorsrma')->__('Request Comments'),
                    'title'     => Mage::helper('vendorsrma')->__('Request Comments'),
                    'content'   => $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tab_message')->toHtml(),
                    //'active'    => true
                ));
                /*
                $this->addTab('addition', array(
                    'label'     => Mage::helper('vendorsrma')->__('Addition Information'),
                    'title'     => Mage::helper('vendorsrma')->__('Addition Information'),
                    'content'   => $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tab_addition')->toHtml(),
                    //'active'    => true
                ));
                */
                if (Mage::app()->getStore()->isAdmin()) {
                    $this->addTab('history', array(
                        'label' => Mage::helper('vendorsrma')->__('Status History'),
                        'title' => Mage::helper('vendorsrma')->__('Status History'),
                        'content' => $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tab_history')->toHtml(),
                    ));
                }

                if(Mage::helper("vendorsrma/config")->allowSendRefund()):
                    if (Mage::app()->getStore()->isAdmin()) {
                        $this->addTab('notes', array(
                            'label' => Mage::helper('vendorsrma')->__('Notes'),
                            'title' => Mage::helper('vendorsrma')->__('Notes'),
                            'content' => $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tab_clamber')->toHtml(),
                        ));
                    }
                    else{
                        $options = Mage::getModel("vendorsrma/status")->getOptions();
                        $types = Mage::getModel("vendorsrma/type")->getOptions();
                        if($request->getStatus() != $options[0]["value"]){
                            if($request->getState() == VES_VendorsRma_Model_Option_State::STATE_BEING 
                                && $request->getType() == $types[0]["value"] && 
                                $request->getFlagState() == 6
                            ){
                                // do some thing
                            }
                            else{
                                    $this->addTab('notes', array(
                                        'label' => Mage::helper('vendorsrma')->__('Notes'),
                                        'title' => Mage::helper('vendorsrma')->__('Notes'),
                                        'content' => $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tab_clamber')->toHtml(),
                                    ));
                            }
                        }
                    }
                endif;

            }

            $this->addTab('item', array(
                'label'     => Mage::helper('vendorsrma')->__('RMA Items'),
                'title'     => Mage::helper('vendorsrma')->__('RMA Items'),
                'content'   => $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tab_item')->toHtml(),
            ));

            $this->addTab('customer', array(
                'label'     => Mage::helper('vendorsrma')->__('Customer Address'),
                'title'     => Mage::helper('vendorsrma')->__('Customer Address'),
                'content'   => $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tab_address')->toHtml(),
            ));
            if(!$request->getId()) {
                $this->addTab('notes', array(
                    'label' => Mage::helper('vendorsrma')->__('Notes'),
                    'title' => Mage::helper('vendorsrma')->__('Notes'),
                    'content' => $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tab_note')->toHtml(),
                ));
            }
        }
        else{
            if (!Mage::app()->getStore()->isAdmin()) {
                $this->addTab('setting', array(
                    'label' => Mage::helper('vendorsrma')->__('Basic RMA Information'),
                    'title' => Mage::helper('vendorsrma')->__('Basic RMA Information'),
                    'content' => $this->getLayout()->createBlock('vendorsrma/vendor_request_edit_tab_basic')->toHtml(),
                ));
            }
            else{
                $this->addTab('setting', array(
                    'label' => Mage::helper('vendorsrma')->__('Basic RMA Information'),
                    'title' => Mage::helper('vendorsrma')->__('Basic RMA Information'),
                    'content' => $this->getLayout()->createBlock('vendorsrma/adminhtml_request_edit_tab_basic')->toHtml(),
                ));
            }
        }


        return parent::_beforeToHtml();
    }
}