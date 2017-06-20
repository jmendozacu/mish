<?php
class VES_VendorsPage_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function match(Zend_Controller_Request_Http $request) 
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        
        $pageId = $request->getPathInfo();
        //remove trailing slash if any
        $pageId = trim($pageId, '/');
		if(Mage::getStoreConfig('vendors/vendor_page/url_key')){
	        //check if we have reserved word in the url
			$pageIds 		= explode('/', $pageId,3);
			$handle 		= isset($pageIds[0])?$pageIds[0]:'';
			$vendor 		= isset($pageIds[1])?$pageIds[1]:'';
			$requestPath 	= isset($pageIds[2])?$pageIds[2]:'';

			if(!(trim($handle) == Mage::getStoreConfig('vendors/vendor_page/url_key'))) return false;
        }else{
			$pageIds = explode('/', $pageId,2);
			$vendor 		= isset($pageIds[0])?$pageIds[0]:'';
			$requestPath 	= isset($pageIds[1])?$pageIds[1]:'';
        }
		
		$vendorObj = Mage::getModel('vendors/vendor')->loadByVendorId($vendor);
		if(!$vendorObj->getId()) return false;
		Mage::register('vendor_id', $vendor);
		Mage::register('vendor', $vendorObj);
		Mage::register('current_vendor', $vendorObj);
		
		$condition = new Varien_Object(array(
            'identifier' => $requestPath,
            'continue'   => true
        ));
        Mage::dispatchEvent('vendor_page_controller_router_match_before', array(
            'router'    => $this,
        	'request'	=> $request,
            'condition' => $condition,
        ));
    	if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }
        
    	if ($condition->getDispatched()) {
            return true;
        }
        
        if (!$condition->getContinue()) {
            return false;
        }
        $targetPath = $requestPath;
		if($requestPath){
	    	/* Process url rewrite */
			$requestPathTmp = $requestPath;
			while(true){
		    	$urlRewrite = Mage::getModel('core/url_rewrite')
		                ->setStoreId(Mage::app()->getStore()->getId())
		                ->loadByRequestPath($requestPathTmp);
		                
		        if ($urlRewrite->getId()){
		        	$targetPath = $urlRewrite->getTargetPath();
		        	$requestPathTmp = $targetPath;
		        }else{
		        	break;
		        }
			}
		}
		
		if(!$targetPath) $targetPath = $requestPath;
		$targetPath = str_replace('catalog', '', $targetPath);
		$targetPath	= trim($targetPath,'/');
		
		$targetPath = explode('/', $targetPath, 3);
		$controller	= isset($targetPath[0]) && $targetPath[0]?$targetPath[0]:'index';
		$action		= isset($targetPath[1]) && $targetPath[1]?$targetPath[1]:'index';
		$params 	= isset($targetPath[2])?$targetPath[2]:'';

		$params 	= trim($params, '/');
		$params		= explode('/', $params);
		$newparams 	= array();
		if(is_array($params)) {
			for($i = 0; $i <sizeof($params); $i += 2){
				$newparams[$params[$i]] = isset($params[$i+1])?$params[$i+1]:"";
			}
		}
		$params = $newparams;
        $realModule = 'VES_VendorsPage';

        $request->setPathInfo('/');
        $request->setModuleName('vendorspage');
        $request->setControllerName($controller);
        $request->setParams($params);
        $request->setActionName($action);
        return true;
    }
}