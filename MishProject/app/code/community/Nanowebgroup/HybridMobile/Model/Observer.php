<?php
/**
 * SiteRestrict Model Observer.
 *
 * @category    NanoWebGroup
 * @package     Nanowebgroup_HybridMobile
 * @version     1.0.0
 * @copyright   
 */
class Nanowebgroup_HybridMobile_Model_Observer
{
    /**
     * 
     *
     * @param Varien_Event_Observer $observer
     */

    public function checkForValidation(Varien_Event_Observer $observer){

        Mage::app()->getCacheInstance()->cleanType('layout');
        if(Mage::helper('hybridmobile')->restrictSiteAccess()){

            $validate = Mage::helper('hybridmobile')->validateAction();
            if($validate) {
                $mobile_agents = '/iPhone|iPod|BlackBerry|Palm|Googlebot-Mobile|mobile|mobi|Windows Mobile|Safari Mobile|Android|Opera Mini/i';
                if (preg_match($mobile_agents, $_SERVER["HTTP_USER_AGENT"])) {
                    Mage::getDesign()->setArea('frontend') //Area (frontend|adminhtml)
                        ->setPackageName('hybridmobile') //Name of Package
                        ->setTheme('default'); // Name of theme
                }
                
            }
        }
    }

    public function cmsPageRender(Varien_Event_Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        $actionName = strtolower($action->getFullActionName());
        
        if(Mage::helper('hybridmobile')->restrictSiteAccess()){

            $validate = Mage::helper('hybridmobile')->validateAction();
            if($validate) {
                $mobile_agents = '/iPhone|iPod|BlackBerry|Palm|Googlebot-Mobile|mobile|mobi|Windows Mobile|Safari Mobile|Android|Opera Mini/i';
                if (preg_match($mobile_agents, $_SERVER["HTTP_USER_AGENT"])) {
                    $action->getLayout()->getUpdate()
                        ->addHandle($actionName . '_after');
                    return $this;
                }
            }
        }
    }
   
}