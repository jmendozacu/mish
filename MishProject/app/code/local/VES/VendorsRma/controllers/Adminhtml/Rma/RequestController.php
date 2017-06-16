<?php

class VES_VendorsRma_Adminhtml_Rma_RequestController extends Mage_Adminhtml_Controller_action
{

	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/sales/orders');
    }
	
	
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('sales')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function allAction() {
        $this->_initAction()
            ->renderLayout();
    }

    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }

    
    public function loadTemplateAction(){
        $template_id = $this->getRequest()->getParam('id');
        $template=Mage::getModel('vendorsrma/mestemplate')->load($template_id);
        echo $template->getData('content_template');
    }
    
    
    /**
     * Initialize Adress parameters by Customer
     *
     * @return VES_VendorsRma_Model_Address
     */

    protected function _initAddress($request){
        $this->_title($this->__('Sales'))
            ->_title($this->__('All RMA'));

        $address    = Mage::getModel('vendorsrma/address')
            ->setStoreId($this->getRequest()->getParam('store', 0));
        $data = array();
        if(!$request->getId()){
            $customerAddress = array();
            #loop to create the array
            $customer = Mage::getModel("customer/customer")->load($request->getCustomerId());
            if($customer->getId() && $request->getCustomerId()) {
                foreach ($customer->getAddresses() as $add) {
                    $customerAddress = $add->toArray();
                    break;
                }

                $data = array(
                    'lastname' => $customer->getLastname(),
                    'firstname' => $customer->getFirstname(),
                    'telephone' => $customer->getTelephone(),
                    'address' => $customerAddress["street"],
                    'city' => $customerAddress["city"],
                    'country_id' => $customerAddress["country_id"],
                    'region' => $customerAddress["region"],
                    'region_id' => $customerAddress["region_id"],
                    'postcode' => $customerAddress["postcode"],
                    'telephone' => $customerAddress["telephone"],
                );

            }
            else{
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

            }
            $address->setData($data);
        }
        if($request->getId()){
            try {
                $address    = Mage::getModel('vendorsrma/address')->getCollection()->addFieldToFilter("request_id",$request->getId())->getFirstItem();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

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
        $this->_title($this->__('Sales'))
            ->_title($this->__('All RMA'));


        $requestId  = (int) $this->getRequest()->getParam('id');
        $request    = Mage::getModel('vendorsrma/request')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if (!$requestId) {
            if ($order_id = (int) $this->getRequest()->getParam('order_id')) {
                $order = Mage::getModel("sales/order")->load($order_id);
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
                }
            }

            if ($pack = $this->getRequest()->getParam('package_opened')) {
                $request->setPackageOpened($pack);
            }
        }


        $vendor = $this->_getSession()->getVendor();

        //$request->setData("vendor_id",$vendor->getId());
        $request->setData("store_id",Mage::app()->getStore()->getStoreId());
        $request->setData('_edit_mode', true);
        if ($requestId) {
            try {
                $request->load($requestId);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        Mage::register('request', $request);
        Mage::register('current_request', $request);

        return $request;


    }
    
    public function markAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('vendorsrma/request')->load($id);
    
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
    
            Mage::register('request_data', $model);
    
            $this->loadLayout();
            $this->_setActiveMenu('sales');
    
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Request Manager'), Mage::helper('adminhtml')->__('Request Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Request News'), Mage::helper('adminhtml')->__('Request News'));
    
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
    
            $this->_addContent($this->getLayout()->createBlock('vendorsrma/adminhtml_request_mark'));
    
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsrma')->__('Request does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    public function saveandsendAction(){
        $request_id     = $this->getRequest()->getParam('id');
        $request  = Mage::getModel('vendorsrma/request')->load($request_id);
        $time= now();
        if($request->getId() || $request_id == 0) {
            if ($data = $this->getRequest()->getPost()) {
                $request->sendEmailNotify("mark-customer",$data["content_template_customer"])
                ->sendEmailNotify("mark-vendor",$data["content_template_vendor"]);
			    $options = Mage::getModel("vendorsrma/status")->getOptions();
				$request->setUpdateAt($time);
				$request->setStatus($options[4]["value"]);
				$request->setState(VES_VendorsRma_Model_Option_State::STATE_CLOSED);
				$request->setFlagState(4);
				$request->save();
				$request->saveNotify();
				$request->saveStatusHistory(VES_VendorsRma_Model_Option_Status_Type::TYPE_ADMIN,Mage::helper("vendorsrma/config")->getContactName());
				 Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorsrma')->__('Rma was successfully resolved'));
				$this->_redirect('*/*/');
            }
        }
        else{
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsrma')->__('Request does not exist'));
            $this->_redirect('*/*/');
        }
    }
    

    public function editAction() {
        $request_id     = $this->getRequest()->getParam('id');
        $request = $this->_initRequest();
        $address = $this->_initAddress($request);
        $model  = Mage::getModel('vendorsrma/request')->load($request_id);

        if ($model->getId() || $request_id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('request_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('sales');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Request Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Request News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('vendorsrma/vendor_request_edit'));
            //  ->_addJs($this->getLayout()->createBlock('vendorsrma/vendor_vendorsrma_edit_js'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsrma')->__('Request does not exist'));
            $this->_redirect('*/*/');
        }
    }


    /**
     *
     *
     * @return VES_VendorsRma_Model_Request
     */
    protected function _initRequestSave(){
        $request = $this->_initRequest();
        $reqeustData = $this->getRequest()->getPost('request');
        if($reqeustData["status"] != $request->getStatus()) $request->setData("change_status_comment",true);
        $request->addData($reqeustData);

        Mage::dispatchEvent(
            'vendors_rma_request_prepare_save',
            array('rma' => $request, 'request' => $this->getRequest())
        );

        return $request;
    }


    public function newAction() {

        $request = $this->_initRequest();
        $address = $this->_initAddress($request);
        $this->_title($this->__('New RMA'));
        
        Mage::dispatchEvent('admin_rma_new_action', array('request' => $request));
        $this->loadLayout();
        
        $this->_setActiveMenu('sales');
        
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Request Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
        
        
        $this->renderLayout();

    }

    public function saveAction() {
        $request = $this->_initRequestSave();
        /* init address for request */
        $address = $this->_initAddress($request);
        $addressData = $this->getRequest()->getPost('address');
        $address->addData($addressData);

        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        if(!$isEdit) $request->setState(VES_VendorsRma_Model_Option_State::STATE_OPEN);

        if ($data = $this->getRequest()->getPost()) {
            /* validate qty item rma */
            $order_item = $this->getRequest()->getPost("orderitems");
           // $validate_qty = Mage::helper("vendorsrma")->validateQtyOrder($order_item,$this->getRequest()->getParam('id'));

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
                Mage::getSingleton('adminhtml/session')->addError($mesage);
                Mage::getSingleton('adminhtml/session')->setRequestData($data);
                if(!$this->getRequest()->getParam('id')) $this->_redirect('*/*/new', array('order_id' => $order->getId() , 'package_opened' => $request->getData("package_opened")));
                else $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            try {
                $request->save();

                /* request save Address */

                $request->saveAddress($address);

                /* request save Item */

                $request->saveItems($order_item);
                /** save comment when change status */
                if(!$isEdit){
                    $request->saveNotify("admin");
                    $request->saveStatusHistory(VES_VendorsRma_Model_Option_Status_Type::TYPE_ADMIN,Mage::helper("vendorsrma")->__("Administrator"));
                }

                /* request save Message */
                $vendor = $this->_getSession()->getVendor();
                $sender = array("from"=>Mage::helper("vendorsrma/config")->getContactNameAdmin($vendor),"to"=>$request->getCustomerName());

                $request->saveMessage($this->getRequest()->getPost("request"),VES_VendorsRma_Model_Source_Message_Type::TYPE_ADMIN,$sender,$isEdit);


                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorsrma')->__('Request was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $request->getId()));
                    return;
                }
                $this->_redirect('*/*/all');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setRequestData($data);
                if($isEdit)
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                else{
                    $this->_redirect('*/*/new', array('order_id' => $order->getId() , 'package_opened' => $request->getData("package_opened")));
                }
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorsrma')->__('Unable to find item to save'));
        $this->_redirect('*/*/all');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('vendorsrma/vendorsrma');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $vendorsrmaIds = $this->getRequest()->getParam('vendorsrma');
        if(!is_array($vendorsrmaIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorsrmaIds as $vendorsrmaId) {
                    $vendorsrma = Mage::getModel('vendorsrma/vendorsrma')->load($vendorsrmaId);
                    $vendorsrma->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($vendorsrmaIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $vendorsrmaIds = $this->getRequest()->getParam('vendorsrma');
        if(!is_array($vendorsrmaIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorsrmaIds as $vendorsrmaId) {
                    $vendorsrma = Mage::getSingleton('vendorsrma/vendorsrma')
                        ->load($vendorsrmaId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($vendorsrmaIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /** ajax Update Attribute Request */

    public function ajaxUpdateAction(){
        $node=$this->getRequest()->getParam('node');
        $id=$this->getRequest()->getParam('id');
        $value=$this->getRequest()->getParam('value');
        $text=Mage::getModel('vendorsrma/request_ajax')->convertText($node,$value,$id);
        if($node =='note'){
        }
        if(is_array($text)) echo json_encode($text);
        else echo $text;
    }

    /** ajax Update Mesage */

    public function ajaxEditMessageAction(){
        $id  = $this->getRequest()->getParam('id');
        $messagebody=$this->getRequest()->getParam('message');
        $message=Mage::getModel('vendorsrma/message')->load($id);
        $message->setData('message',$messagebody)->setData('update_time',now())->save();
        if(Mage::helper('vendorsrma')->isHtmlMessage($message->getData('message'))){
            $text = $message->getData('message');
        }
        else{
            $text ="<pre>".$message->getData('message').'</pre>';
        }
        echo $text;
    }

    public function exportCsvAction()
    {
        $fileName   = 'vendorsrma.csv';
        $content    = $this->getLayout()->createBlock('vendorsrma/adminhtml_vendorsrma_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'vendorsrma.xml';
        $content    = $this->getLayout()->createBlock('vendorsrma/adminhtml_vendorsrma_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}