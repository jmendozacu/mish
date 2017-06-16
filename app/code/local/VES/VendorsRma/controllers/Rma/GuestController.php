<?php

class VES_VendorsRma_Rma_GuestController extends Mage_Core_Controller_Front_Action
{

    public function _initGuestAction(){
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if( $customer &&  $customer->getId()){
            $this->_redirect('vesrma/rma_customer/new');
        }

        if(!Mage::helper("vendorsrma/config")->allowGuestsRequest()){
            $this->_redirect('vesrma/rma_customer/new');
        }
    }

    public function indexAction(){
        $this->_initGuestAction();
        $this->loadLayout();
        $this->renderLayout();
    }


    public function continueAction(){
        $this->_initGuestAction();
        $this->loadLayout();
        $this->renderLayout();
    }


    public function saveNoteAttacmentAction(){
        $id = $this->getRequest()->getParam('id');
        $note_id = $this->getRequest()->getParam('note_id');
        $request = Mage::getModel('vendorsrma/request')->load($id);
        $note = Mage::getModel("vendorsrma/note")->load($note_id);
        $post = $this->getRequest()->getPost("note");
        if($request->getId() && $note->getId() ){
                   
            $newFiles = Mage::getModel("vendorsrma/source_file")->uploadFileNote($post["filename"]);
            $attachments = Mage::getModel("vendorsrma/source_file")->addMoreActachment($note->getData("attachment"),$newFiles);
    
            if(count($newFiles)) {
               $note->setData("attachment",$attachments);
            }
            else{
                Mage::getSingleton('core/session')->addError(Mage::helper('vendorsrma')->__("Do not have any file upload"));
                $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
                return;
            }
    
            $note->setData("attachment",$attachments);
            try {
                $note->save();
               
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__('Upload successfully'));
                Mage::getSingleton('core/session')->setFormData(false);
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('vendorsrma')->__($e->getMessage()));
            }
           $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
            return;
        }
        Mage::getSingleton('core/session')->addError(Mage::helper('vendorsrma')->__("Request or Note do not exist"));
        $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
        return;
    }
    
    
    public function saveNoteAction(){
        $time = now();
        $id=$this->getRequest()->getParam('id');
        $request = Mage::getModel('vendorsrma/request')->load($id);
        $post = $this->getRequest()->getPost("note");
        if($request->getId()){
            $files =  Mage::getModel("vendorsrma/source_file")->uploadFileNote($post["filename"]);;
            if(!$post["message"]) {
                Mage::getSingleton('vendors/session')->addError(Mage::helper('vendorsrma')->__("Error Attachment"));
                $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
                return;
            }
            $note = Mage::getModel("vendorsrma/note");
            $message=array(
                'message'=>$post["message"],
                'attachment'=> $files ? implode(",",$files) : "",
                'created_time'=> now(),
                'type'=>VES_VendorsRma_Model_Source_Message_Type::TYPE_CUSTOMER,
                'request_id'=>$request->getId(),
            );
            $note->setData($message)->setId();
            try {
                $note->save();
                if($request->getState() == VES_VendorsRma_Model_Option_State::STATE_OPEN){
                    $request->setState(VES_VendorsRma_Model_Option_State::STATE_AWAITING);
                    $request->setFlagState(2);
                }
                else{
                    if($request->getState() == VES_VendorsRma_Model_Option_State::STATE_AWAITING){
                        $request->setState(VES_VendorsRma_Model_Option_State::STATE_BEING);
                        $request->setFlagState(3);
                    }
                }
                $checkS = false;
                $options = Mage::getModel("vendorsrma/status")->getOptions();
                if($request->getStatus() != $options[5]["value"]){
                    $checkS = true;
                    $request->setUpdateAt($time);
                    $request->setStatus($options[5]["value"]);
                    $request->save();
                    $request->saveNotify("admin");
                    $request->saveStatusHistory(VES_VendorsRma_Model_Option_Status_Type::TYPE_CUSTOMER,$request->getCustomerName());
                }
                if(!$checkS)  $request->save();
                
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__('Rma was successfully send note'));
                Mage::getSingleton('core/session')->setFormData(false);

            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('vendorsrma')->__($e->getMessage()));

            }
            $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
            return;
        }

        Mage::getSingleton('core/session')->addError(Mage::helper('vendorsrma')->__("Request do not exist"));
        $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
        return;

    }


    public function noteAction(){
        $this->loadLayout();
        $time = now();
        $id=$this->getRequest()->getParam('id');
        $request = Mage::getModel('vendorsrma/request')->load($id);

        
        $note = Mage::getModel("vendorsrma/note")->getCollection()->addFieldToFilter("request_id",$request->getId())
        ->addFieldToFilter("type",VES_VendorsRma_Model_Source_Message_Type::TYPE_CUSTOMER)->getFirstItem();
        ;
        if($note->getId()){
             $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
            return;
        }
        
        
        if($request->getId()){
            Mage::register('request', $request);
            $this->renderLayout();
        }
        else{
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__('Request do not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction(){
        $this->_initGuestAction();
        $data = $this->getRequest()->getPost();
        $date = Mage::helper("vendorsrma/config")->orderExpiryDay();
        $lastdate = Mage::getModel('core/date')->date('Y-m-d', strtotime("-".$date." days"));
        if ($data && !Mage::registry("order_continue")) {
            Mage::getSingleton('core/session')->setRequestData(false);
            $order = Mage::getModel("sales/order")->loadByIncrementId($data["order_incremental_id"]);
            if($order->getId() && $data["order_incremental_id"]){
                if($order->getCustomerId()){
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__("This order has been placed by registered customer. Please, authorize and request RMA via customer account."));
                    $this->_redirect('*/*/index');
                    return;
                }

                if($order->getCreatedAt() < $lastdate){
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__("The order were overdue ".$date." days"));
                    $this->_redirect('*/*/index');
                    return;
                }

                if($order->getCustomerEmail() != $data["email"]){
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__("Order ID and customer email didn't match each other"));
                    $this->_redirect('*/*/index');
                    return;
                }
                else{
                    Mage::unregister("order_continue");
                    Mage::register("order_continue",$order);
                    $this->_forward("continue");
                    return;
                }
            }
            else{
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__("Couldn't load order by given order ID"));
                $this->_redirect('*/*/index');
                return;
            }
        }
        else{
            if(Mage::getSingleton('core/session')->getRequestData()){
                $data = Mage::getSingleton('core/session')->getRequestData();
                if($data["order_incremental_id"]){
                    $order = Mage::getModel("sales/order")->loadByIncrementId($data["order_incremental_id"]);
                    Mage::unregister("order_continue");
                    Mage::register("order_continue",$order);
                    $this->_forward("continue");
                }
                return;
            }
        }

        $this->_redirect('*/*/index');
    }

    public function viewAction(){
        $this->loadLayout();
        $time = now();
        $id = $this->getRequest()->getParam('sc');
        $request=Mage::getModel('vendorsrma/request')->getCollection()->addAttributeToFilter("uid",$id)->getFirstItem();

        if($request->getId()){
            Mage::register('request', $request);
            $this->renderLayout();
        }
        else{
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__('Request do not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function postreplyAction(){
        $id=$this->getRequest()->getParam('id');
        if ($data = $this->getRequest()->getPost('request')) {
            $time= now();
            $type= VES_VendorsRma_Model_Source_Message_Type::TYPE_CUSTOMER;
            $request = Mage::getModel('vendorsrma/request')->load($id);

            $from = $request->getData('customer_name') ?  $request->getData('customer_name') : "";
            $vendor = Mage::getModel("vendors/vendor")->load($request->getVendorId());
            $to = Mage::helper("vendorsrma/config")->getContactNameVendor($vendor);
            $sender = array("from"=>$from,"to"=>$to);
            try {
                $request->setUpdateAt($time);
                $request->save();

                $request->saveMessage($data,$type,$sender,true);

                Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__('Rma was successfully saved'));
                Mage::getSingleton('core/session')->setRequestData(false);

                $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('vendorsrma')->__($e->getMessage()));
                $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
            }
        }
    }

    public function cancelAction(){
        $id=$this->getRequest()->getParam('id');
        $time= now();
        $request = Mage::getModel('vendorsrma/request')->load($id);
        try {
            $options = Mage::getModel("vendorsrma/status")->getOptions();
            $request->setUpdateAt($time);
            $request->setStatus($options[3]["value"]);
            $request->setState(VES_VendorsRma_Model_Option_State::STATE_CLOSED);
            $request->save();
            $request->saveNotify();
            $request->saveStatusHistory(VES_VendorsRma_Model_Option_Status_Type::TYPE_CUSTOMER,$request->getCustomerName());
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__('Rma was successfully canceled'));
            Mage::getSingleton('core/session')->setRequestData(false);

        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError(Mage::helper('vendorsrma')->__($e->getMessage()));
        }

        $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
    }


    public function confirmshipAction(){
        $id=$this->getRequest()->getParam('id');
        $time= now();
        $request = Mage::getModel('vendorsrma/request')->load($id);
        try {
            $options = Mage::getModel("vendorsrma/status")->getOptions();
            $request->setUpdateAt($time);
            $request->setStatus($options[2]["value"]);
            $request->save();
            $request->saveNotify();
            $request->saveStatusHistory(VES_VendorsRma_Model_Option_Status_Type::TYPE_CUSTOMER,$request->getCustomerName());
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__('Rma was successfully confirm shipping'));
            Mage::getSingleton('core/session')->setFormData(false);

        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError(Mage::helper('vendorsrma')->__($e->getMessage()));

        }
        $this->_redirect('*/*/view',array('sc'=>$request->getData("uid")));
    }

    /** ajax load order  */

    public function ajaxproductAction(){
        $order_id  = $this->getRequest()->getParam('order');
        $order=Mage::getModel('sales/order')->loadByIncrementId($order_id);
        Mage::register('order', $order);
        $layout = $this->loadLayout();
        $this->renderLayout();
    }



    /**
     * Initialize Adress parameters by Customer
     *
     * @return VES_VendorsRma_Model_Address
     */

    protected function _initAddress($request){
        $address    = Mage::getModel('vendorsrma/address')
            ->setStoreId($this->getRequest()->getParam('store', 0));
        $data = array();

        $customerAddress = array();
        #loop to create the array

        $order = Mage::getModel("sales/order")->loadByIncrementId($request->getData("order_incremental_id"));

        if($order->getId()){
            $billingAddress = Mage::getModel('sales/order_address')->load($order->getBillingAddress()->getId());

            $data = array(
                'lastname' => $billingAddress->getLastname(),
                'firstname' => $billingAddress->getFirstname(),
                'telephone' => $billingAddress->getTelephone(),
                'address' => $billingAddress->getData("street"),
                'city' => $billingAddress->getData("city"),
                'country_id' => $billingAddress->getData("country_id"),
                'region' => $billingAddress->getData("region"),
                'region_id' => $billingAddress->getData("region_id"),
                'postcode' => $billingAddress->getData("postcode"),
                'telephone' => $billingAddress->getData("telephone"),
                'company' => $billingAddress->getData("company"),
            );
        }

        $address->setData($data);

        Mage::register('addresss', $address);
        Mage::register('current_addresss', $address);

        return $address;
    }


    /**
     * Initialize Request from request parameters
     *
     * @return VES_VendorsRma_Model_Request
     */
    protected function _initRequest()
    {

        $request    = Mage::getModel('vendorsrma/request')
            ->setStoreId($this->getRequest()->getParam('store', 0));
        if ($order_id = (int) $this->getRequest()->getParam('order_incremental_id')) {
            $order = Mage::getModel("sales/order")->loadByIncrementId($order_id);
            if($order->getId()){
                $request->setData("order_incremental_id",$order->getIncrementId());
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                if(!$order->getCustomerId() || !$customer->getId()){
                    $customer_name = $order->getBillingAddress()->getFirstname()." ".$order->getBillingAddress()->getLastname();
                }
                else{
                    $customer_name = $customer->getName();
                    $request->setData("customer_id",$order->getCustomerId());
                }
                $request->setData("customer_name",$customer_name);
                $request->setData("customer_email",$order->getCustomerEmail());
                if($order->getVendorId()) $request->setData("vendor_id",$order->getVendorId());
            }
        }

        if ($pack = $this->getRequest()->getParam('package_opened')) {
            $request->setPackageOpened($pack);
        }


        $request->setData("store_id",Mage::app()->getStore()->getStoreId());

        Mage::register('request', $request);
        Mage::register('current_request', $request);

        return $request;


    }

    /**
     *
     *
     * @return VES_VendorsRma_Model_Request
     */
    protected function _initRequestSave(){
        $request = $this->_initRequest();
        $reqeustData = $this->getRequest()->getPost('request');
        $options = Mage::getModel("vendorsrma/status")->getOptions();
        $request->setStatus($options[0]["value"]);
        $request->setState(VES_VendorsRma_Model_Option_State::STATE_OPEN);
        
        if($reqeustData["reason"] == "other") $reqeustData["reason"] = "";
        $request->addData($reqeustData);

        Mage::dispatchEvent(
            'vendors_rma_request_prepare_save',
            array('rma' => $request, 'request' => $this->getRequest())
        );

        return $request;
    }

    public function saveAction(){
        $request = $this->_initRequestSave();
        /* init address for request */
        $address = $this->_initAddress($request);
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        if ($data = $this->getRequest()->getPost()) {
            /* validate qty item rma */
            $order_item_keys = $this->getRequest()->getPost("orderitems");

            $order_item_counts = $this->getRequest()->getPost("orderitemscount");
            foreach($order_item_keys as $key=>$value){
                foreach($order_item_counts as $key1=>$value1){
                    if($key == $key1) $order_item[$key1] = $value1;
                }
            }

            $order = Mage::getModel("sales/order")->loadByIncrementId($request->getData("order_incremental_id"));
            $validate_qty = Mage::helper("vendorsrma")->validateQtyOrder($order_item,$order->getStatus(),$this->getRequest()->getParam('id'));
            
            if($validate_qty != VES_VendorsRma_Helper_Data::QTY_APECPT){

                switch($validate_qty){
                    case VES_VendorsRma_Helper_Data::EXEPTION_QTY_ZERO:
                        $mesage = Mage::helper("vendorsrma")->__("Request should have at least one item");
                        break;
                    case VES_VendorsRma_Helper_Data::EXEPTION_QTY_MUCH:
                        $mesage = Mage::helper("vendorsrma")->__("Wrong Qty Item");
                        break;
                }
                Mage::getSingleton('core/session')->addError($mesage);
                Mage::getSingleton('core/session')->setRequestData($data);
                $this->_redirect('*/*/new');
                return;
            }
            try {
                $request->save();

                /* request save Address */

                $request->saveAddress($address);

                /* request save Item */

                $request->saveItems($order_item);
                /** save comment when change status */
                $request->saveNotify();
                $request->saveStatusHistory(VES_VendorsRma_Model_Option_Status_Type::TYPE_CUSTOMER,$request->getCustomerName());
                /* request save Message */
                $vendor = Mage::getModel("vendors/vendor")->load($request->getVendorId());
                $sender = array("to"=>Mage::helper("vendorsrma/config")->getContactNameVendor($vendor),"from"=>$request->getCustomerName());

                $request->saveMessage($this->getRequest()->getPost("request"),VES_VendorsRma_Model_Source_Message_Type::TYPE_CUSTOMER,$sender,$isEdit);


                Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__('Request was successfully saved'));
                Mage::getSingleton('core/session')->setRequestData(false);

            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::getSingleton('core/session')->setRequestData($data);
                $this->_redirect('*/*/new');
                return;
            }
        }
        $this->_redirect('*/*/index');
    }


    public function printAction(){
        $this->loadLayout();
        $time = now();
        $id=$this->getRequest()->getParam('id');
        $request = Mage::getModel('vendorsrma/request')->load($id);

        if($request->getId()){
            Mage::register('request', $request);
            $this->_initLayoutMessages('customer/session');
            $this->renderLayout();
        }
        else{
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__('Request do not exist'));
            $this->_redirect('*/*/');
        }
    }


    public function printformAction(){
        $time = now();
        $id=$this->getRequest()->getParam('id');
        $request = Mage::getModel('vendorsrma/request')->load($id);

        $data = $this->getRequest()->getPost("printlabel");
        if($request->getId()){
            $this->loadLayout('print_rma');
            Mage::register('request', $request);
            $customer_info = new Varien_Object();
            $customer_info->setData($data);
            Mage::register('customer_info', $customer_info);
            $this->renderLayout();
        }
        else{
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('vendorsrma')->__('Request do not exist'));
            $this->_redirect('*/*/');
        }

    }




}