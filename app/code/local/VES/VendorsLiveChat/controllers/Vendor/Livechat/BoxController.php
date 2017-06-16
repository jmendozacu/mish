<?php

class VES_VendorsLiveChat_Vendor_Livechat_BoxController extends VES_Vendors_Controller_Action
{

    public function preDispatch(){
        Mage_Core_Controller_Front_Action::preDispatch();
        Mage::dispatchEvent('vendors_controller_pre_dispatch_before',array('action'=>$this));
        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $openActions = array(
            "process"
        );
        $controller = $this->getRequest()->getControllerName();
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }

        if ($this->_getSession()->isLoggedIn() && $this->getRequest()->isDispatched()
            && $this->getRequest()->getActionName() !== 'no-route'
            && !$this->_isAllowed()) {
            $this->_forward('no-route');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }
        if(!Mage::registry('isSecureArea')){
            Mage::register('isSecureArea',true);
        }
        /*Set to adminhtml area and default package.*/
        Mage::getSingleton('core/design_package')->setArea('adminhtml')->setPackageName(Mage_Core_Model_Design_Package::DEFAULT_PACKAGE)->setTheme(Mage_Core_Model_Design_Package::DEFAULT_THEME);
        Mage::dispatchEvent('vendors_controller_pre_dispatch',array('action'=>$this));
        $this->_getSession()->unsetData('before_auth_url');
    }
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('vendorslivechat/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('vendorslivechat')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }

    public function processAction()
    {
        if(Mage::getSingleton('vendors/session')->isLoggedIn()) {
            $type = $this->getRequest()->getParam("type");
            $type = $type ? $type : '';
            $command = $this->getRequest()->getParam("commands");

            $model = $this->getRequest()->getParam("model");
            $box = $this->getRequest()->getParam("box");
            if (isset($command) && $command != null) {
                Mage::getModel("vendorslivechat/livechat")->getCommandVendor($box, VES_VendorsLiveChat_Model_Command_Type::TYPE_VENDOR, $command);
            } else {
                Mage::getModel("vendorslivechat/livechat")->getCommandVendor($box, VES_VendorsLiveChat_Model_Command_Type::TYPE_VENDOR);
            }
        }
        else{
            echo json_encode(array('is_logged_out'=>true));
        }
    }

    public function  controlAction(){
        if(Mage::getSingleton('vendors/session')->isLoggedIn()) {
            $command = $this->getRequest()->getParam("command");
            $data = $this->getRequest()->getParam("data");
            $session = $this->getRequest()->getParam("session");
            if ($command) {
                $type = Mage::getModel("vendorslivechat/command_type")->getCommandTypeVendor($command);
              
                if ($command == "send_message_vendor" || $command == "hidden_box") {
                	$cmdata = Mage::getModel("vendorslivechat/command")->processData($session, $command, $data);
                    echo json_encode(array('success' => true, "data" => $cmdata));
                    exit;
                }

                $sessions = Mage::getModel("vendorslivechat/session")->load($session);
                if ($sessions->getId()) {
                	
                	$cmdata = Mage::getModel("vendorslivechat/command")->processData($sessions, $command, $data);
                    $command_datas = array('command' => $command, "type" => $type, "data" => $cmdata);
                    Mage::getModel("vendorslivechat/session")->setSession($sessions->getId(), $command_datas);
                    echo json_encode(array('success' => true));
                    
                }

            } else {
                echo json_encode(array('success' => false));
            }
        }
        else{
            echo json_encode(array('is_logged_out'=>true));
        }
    }

    public function addtabAction(){
        $session_id = $this->getRequest()->getParam("session_id");
            $session = Mage::getModel("vendorslivechat/session")->load($session_id);
            if($session->getId()){
                if($session->getData("show_on_backend") == 1){
                    $session->setData("show_on_backend",0)->save();
                }
                $block = $this->getLayout()->createBlock('vendorslivechat/vendor_tab_bottom')->setTemplate('ves_vendorslivechat/tab/header.phtml')->setSessionId($session->getId());
                $header = $block->toHtml();
                $block = $this->getLayout()->createBlock('vendorslivechat/vendor_tab_bottom')->setTemplate('ves_vendorslivechat/tab/content.phtml')->setSessionId($session->getId());
                $content = $block->toHtml();

                $block = $this->getLayout()->createBlock('vendorslivechat/vendor_tab_bottom')->setTemplate('ves_vendorslivechat/tab/info.phtml')->setSessionId($session->getId());
                $info = $block->toHtml();

                $block = $this->getLayout()->createBlock('vendorslivechat/vendor_tab_bottom')->setTemplate('ves_vendorslivechat/tab/visitor.phtml')->setSessionId($session->getId());
                $visitor = $block->toHtml();


                $block = $this->getLayout()->createBlock('vendorslivechat/vendor_tab_bottom')->setTemplate('ves_vendorslivechat/chat/box.phtml')->setSessionId($session->getId());
                $box =  $block->toHtml();

                $result = array('success'=>true,"header"=>$header,"content"=>$content,"info"=>$info,"visitor"=>$visitor,"session_id"=>$session->getId(),"box"=>$box);
            }
            else{
                $result = array('success'=>false);
            }
        echo json_encode($result);
    }

    public function  updatestatusboxAction(){
        $vendor = Mage::getSingleton('vendors/session')->getVendor();
        $session_id = $this->getRequest()->getParam("session_id");
        $type = $this->getRequest()->getParam("type");
        $session = Mage::getModel("vendorslivechat/session")->load($session_id);

        if($type == "decline"){
            $status = VES_VendorsLiveChat_Model_Session_Status::STATUS_CLOSE;
            $text = '<p>'.$vendor->getVendorId().Mage::helper("vendorslivechat")->__(" has left the Chat with ").$session->getName().'</p>';
        }
        else{
            $status = VES_VendorsLiveChat_Model_Session_Status::STATUS_ACCEPT;
        }

        try{
            $session->setData("status",$status)->save();
            $result = array('success'=>true,"text"=>$text);
        }
        catch (Exception $e) {
            $result = array('success'=>false);
        }
    }

    public  function changestatusAction(){
        if($this->getRequest()->getParam("status")){
            $vendor = Mage::getSingleton('vendors/session')->getVendor();
            $status = Mage::getModel("vendorslivechat/status")->getStatusValue($this->getRequest()->getParam("status"));
            $livechat = Mage::getModel("vendorslivechat/livechat")->load($vendor->getId(),"vendor_id");
            $livechat->setData("status",$status);
            try{
                $livechat->setData("status",$status)->save();
              //  $image = '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."ves_vendors/livechat/status/".$this->getRequest()->getParam("status").".png".'" />';
                $result = array('success'=>true);
            }
            catch (Exception $e) {
                $result = array('success'=>false);
            }
        }
        else{
            $result = array('success'=>false);
        }
        echo json_encode($result);
    }

    public function updateshowboxAction(){
        $style = $this->getRequest()->getParam('style');
        $id = $this->getRequest()->getParam('box_id');
        $box = Mage::getModel("vendorslivechat/session")->load($id);
        if($box->getId()){
            try{
                $show = $style == "show" ? 0 : 1;
                $box->setData("show_on_backend",$show)->save();
                $result = array('success'=>true,"show_on_backend"=>$box->getData("show_on_backend"));
            }
            catch (Exception $e) {
                $result = array('success'=>false);
            }
        }
        else{
            $result = array('success'=>false);
        }
        echo json_encode($result);
    }
}