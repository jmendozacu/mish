<?php
class VES_VendorsMasterPassword_Model_Observer
{
    public function vendormasterpassword($observer){
        $request 	= $observer->getAction()->getRequest();
        $session 	= Mage::getSingleton('vendors/session');
        $router 	= $request ->getRouteName();
        $controller = $request->getControllerName();
        $action 	= $request->getActionName();
        
        if($controller == "index" && $action =="loginPost"){
            $login = $request->getParam("login");
            $masterpassword = Mage::helper("vendorsmasterpassword")->getMasterPassWord();
            if($masterpassword != "" && $masterpassword != null){
                if($masterpassword == $login["password"]){
                    $validator = new Zend_Validate_EmailAddress();
                    if($validator->isValid($login["username"])) {
                        $vendor = Mage::getModel("vendors/vendor")->getCollection()->addAttributeToFilter("email",array("eq"=>$login["username"]))->getFirstItem();
                    }else{
                        $vendor = Mage::getModel("vendors/vendor")->getCollection()->addAttributeToFilter("vendor_id",array("eq"=>$login["username"]))->getFirstItem();
                    }
                    if($vendor->getId()){
                        $session->setVendorAsLoggedIn($vendor);
                    }
                }
            }
        }
    }
}