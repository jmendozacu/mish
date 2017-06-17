<?php

class OTTO_AdvancedFaq_Model_Observer
{
	public function initControllerRouters($observer) 
    {
    	//return true;
    	$request = $observer->getData("request");
    	//var_dump($request);exit;
    	$condition_ob = $observer->getData("condition");

    	//$condition = $observer->getData("condition");
    	//var_dump($condition);exit;
    	$identifierUrl = $condition_ob->getData('identifier');
    	$identifiers = explode("/", $identifierUrl);
    	
    	if($identifiers[0] != Mage::getStoreConfig('advancedfaq/config/url_key')){
    		//$condition->setRedirectUrl("faq/home/voteajax");
    		return true;
    	}
    	if(sizeof($identifiers) == 1){
    			$pageType 	= 'kbase_home';
    		}elseif(sizeof($identifiers) == 2){
    			if($identifiers[1] == "new"){
    				$identifier = $identifiers[1];
    				$pageType 	= 'news';
    			}
    			else{
    				if($identifiers[1] == "search"){
    					$identifier = $identifiers[1];
    					$pageType 	= 'searchs';
    				}
    				else{
    					$identifier = $identifiers[1];
    					$pageType 	= 'category';
    				}
    			}
    		}elseif($identifiers[2]=='vote'){
    			$pageType 	= 'votes';
    			$id = $identifiers[3];
    			$value = $identifiers[4];
    		}
    		else{
    			$catId 		= $identifiers[1];
    			$identifier = $identifiers[2];
    			$pageType 	= 'article';
    		}
    		 

    		
    		$condition = new Varien_Object(array(
    				'identifier' => $identifier,
    				'continue'   => true
    		));
    		Mage::dispatchEvent('advancedfaq_controller_router_match_before', array(
    		'router'    => $this,
    		'condition' => $condition
    		));
    		
    		if ($condition->getRedirectUrl()) {
    			Mage::app()->getFrontController()->getResponse()
    			->setRedirect($condition->getRedirectUrl())
    			->sendResponse();
    			$request->setDispatched(true);
    			return true;
    		}
    		 
    		if (!$condition->getContinue()) {
    			return false;
    		}
    		 
    		$suffix = '';

    		$identifier = $condition->getIdentifier();
    		$storeId	= Mage::app()->getStore()->getId();
    		
    	
    		
    		if($pageType == 'kbase_home'){

    			$request->setModuleName('sellerspage')
    			->setControllerName('home')
    			->setActionName('index');

    			$condition_ob->setDispatched(true);
    		
    			return true;
    		}elseif($pageType=='category'){
    			/*Category Page */
    			$suffix = Mage::getStoreConfig('advancedfaq/config/category_suffix');
    			$tmpIdentifier 	= trim(str_replace($suffix, "", $identifier));
    			 
    			$category 		= Mage::getModel('advancedfaq/category')->checkIdentifier($tmpIdentifier, $storeId);
    			 
    			if (!$category->getId()) {
    				return false;
    			}

    			
    			
    			Mage::register('kbase_category', $category);
    			Mage::register('kbase_current_category', $category);
    			 
    			$request->setModuleName('sellerspage')
    			->setControllerName('category')
    			->setActionName('index')
    			->setParam('identifier', $category->getUrlKey());
    			$condition_ob->setDispatched(true);
    		}else{if($pageType=='votes'){
    			$request->setModuleName('sellerspage')
    			->setControllerName('home')
    			->setActionName('vote')
    			->setParam('id', $value);
    			$condition_ob->setDispatched(true);
    		}
    		else{
    			if($pageType=='news'){
    				$request->setModuleName('sellerspage')
    				->setControllerName('new')
    				->setActionName('index');
    				$condition_ob->setDispatched(true);
    	;
    			}
    			else{
    				if($pageType=='searchs'){
    					$request->setModuleName('sellerspage')
    					->setControllerName('search')
    					->setActionName('index');
    					$condition_ob->setDispatched(true);
    				}
    			}
    		}
    		}
    		
    	
    	
    		$request->setAlias(
    				Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
    				$identifierUrl
    		);
    		
    		
    	
    		
    		return true;
    }
    
}