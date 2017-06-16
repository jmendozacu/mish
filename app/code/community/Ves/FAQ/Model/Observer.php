<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves FAQ extension
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FAQ_Model_Observer  extends Varien_Object
{

  public function initControllerRouters($observer){

    if(Mage::helper("ves_faq")->isAdmin()) {
      return;
    }

    $request = $observer->getEvent()->getFront()->getRequest();
    if (!Mage::app()->isInstalled()) {
      return;
    }
    $identifier = trim($request->getPathInfo(), '/');

    $condition = new Varien_Object(array(
      'identifier' => $identifier,
      'continue'   => true
      ));
    Mage::dispatchEvent('faq_controller_router_match_before', array(
      'router'    => $this,
      'condition' => $condition
      ));
    $identifier = $condition->getIdentifier();
    $identifier = trim($identifier, "/");

    if ($condition->getRedirectUrl()) {
      Mage::app()->getFrontController()->getResponse()
      ->setRedirect($condition->getRedirectUrl())
      ->sendResponse();
      $request->setDispatched(true);
      return true;
    }

    if (!$condition->getContinue())
      return false;

    $route = trim(Mage::getStoreConfig('ves_faq/general_setting/route') );
    $module_enable = Mage::getStoreConfig('ves_faq/general_setting/enable');

    if($identifier) {

      if(  preg_match("#^".$route."(\.html)?$#",$identifier, $match)) {
        $request->setModuleName('venusfaq')
        ->setControllerName('index')
        ->setActionName('index');
        $request->setAlias(
          Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
          $identifier
          );
        return true;
      }

      $idarray = explode('/',$identifier);
      if($idarray[0] == $route && $idarray[1] == 'search' && $module_enable){
        $request->setModuleName('venusfaq')
        ->setControllerName('search')
        ->setActionName('index');

        $request->setAlias(
          Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,$identifier
          );

        return true;
      }

      if ($idarray[0] == $route){
        $idarray[1] = str_replace('.html', '', $idarray[1]);
        $storeId = Mage::app()->getStore()->getId();
        $category = Mage::getModel('ves_faq/category')->getCollection()
              ->addFieldToFilter('identifier', $idarray[1])
              ->addFieldToFilter('status', array('eq'=>1))
              ->addFieldToFilter('status', array('visibility'=>1))
              ->addStoreFilter()->getFirstItem();
        $data = $category->getData();
        if (!$category->getCategoryId() || !$module_enable) {
          return false;
        }

        $request->setModuleName('venusfaq')
        ->setControllerName('category')
        ->setActionName('view')
        ->setParam('category_id', $category->getCategoryId());

        $request->setAlias(
          Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,$identifier
          );

        return true;

      }
    }
    return false;
  }
}
