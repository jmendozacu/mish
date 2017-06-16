<?php

class VES_VendorsSubAccount_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Is Module Enable
	 */
	public function moduleEnable(){
		$result = new Varien_Object(array('module_enable'=>true));
		Mage::dispatchEvent('ves_vendorssubaccount_module_enable',array('result'=>$result));
		return $result->getData('module_enable');
	}
	
	/**
     * Retrieve vendor account page url
     *
     * @return string
     */
    public function getAccountUrl()
    {
    	if(Mage::getSingleton('vendors/session')->getIsSubAccount()) return $this->_getUrl('vendors/subaccount_profile');
        return $this->_getUrl('vendors/account');
    }
    
    public function getResourcesList(){
    	$this->_buildResourcesArray();
    }
    
	/**
     * Build resources array process
     *
     * @param  null|Varien_Simplexml_Element $resource
     * @param  null $parentName
     * @param  int $level
     * @param  null $represent2Darray
     * @param  bool $rawNodes
     * @param  string $module
     * @return array|null|Varien_Simplexml_Element
     */
    protected function _buildResourcesArray(Varien_Simplexml_Element $resource = null,
        $parentName = null, $level = 0, $represent2Darray = null, $rawNodes = false, $module = 'adminhtml')
    {
        static $result;
        if (is_null($resource)) {
            $resource = Mage::getConfig()->getNode('vendors/menu');
            $modules = Mage::getConfig()->getNode('modules')->asArray();
            if(isset($modules['VES_VendorsMessage'])
            	&& isset($modules['VES_VendorsMessage']['active'])
            	&& $modules['VES_VendorsMessage']['active']=='true'
            	&& !$resource->descend('message')){
	            $messageNode = $resource->addChild('message');
				$messageNode->addChild('title',$this->__('Message'));
				$messageNode->addChild('sort_order',1);
            }
        	if(!$resource->descend('configuration')){
	            $messageNode = $resource->addChild('configuration');
				$messageNode->addChild('title',$this->__('Configuration'));
				$messageNode->addChild('sort_order',2);
            }
            
            Mage::dispatchEvent('ves_vendorssubaccount_prepare_permission_xml',array('resource'=>$resource));
            
            $resourceName = null;
            $level = -1;
        } else {
            $resourceName = $parentName;
            if (!in_array($resource->getName(), array('title', 'sort_order', 'children', 'disabled'))) {
                $resourceName = (is_null($parentName) ? '' : $parentName . '/') . $resource->getName();

                //assigning module for its' children nodes
                if ($resource->getAttribute('module')) {
                    $module = (string)$resource->getAttribute('module');
                }

                if ($rawNodes) {
                    $resource->addAttribute("aclpath", $resourceName);
                    $resource->addAttribute("module_c", $module);
                }

                if ( is_null($represent2Darray) ) {
                    $result[$resourceName]['name']  = Mage::helper($module)->__((string)$resource->title);
                    $result[$resourceName]['level'] = $level;
                } else {
                    $result[] = $resourceName;
                }
            }
        }


		//check children and run recursion if they exists
        $children = $resource->children();
        foreach ($children as $key => $child) {
            if (1 == $child->disabled) {
                $resource->{$key} = null;
                continue;
            }
            $this->_buildResourcesArray($child, $resourceName, $level + 1, $represent2Darray, $rawNodes, $module);
        }

        if ($rawNodes) {
            return $resource;
        } else {
            return $result;
        }
    }
    
	/**
     * Return tree of acl resources
     *
     * @return array|null|Varien_Simplexml_Element
     */
    public function getResourcesTree()
    {
        return $this->_buildResourcesArray(null, null, null, null, true);
    }
}