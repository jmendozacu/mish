<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Search Controller
 */
class VES_VendorsCatalogSearch_SearchresultController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve catalog session
     *
     * @return Mage_Catalog_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('catalog/session');
    }
    /**
     * Display search result
     */
    public function indexAction()
    {
    	if(!Mage::helper('vendorsconfig')->getVendorConfig('catalogsearch/config/enabled',Mage::registry('vendor')->getId())){
    		$this->_forward('noroute');
    		return;
    	}
        $query = Mage::helper('vendorscatalogsearch')->getQuery();
       
        /* @var $query Mage_CatalogSearch_Model_Query */

        $query->setStoreId(Mage::app()->getStore()->getId());
        if ($query->getQueryText() != '') {
            if (Mage::helper('vendorscatalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            }
            else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                }
                else {
                    $query->setPopularity(1);
                }

                if ($query->getRedirect()){
                    $query->save();
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                }
                else {
                    $query->prepare();
                }
            }

            Mage::helper('vendorscatalogsearch')->checkNotes();

            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
            if (!Mage::helper('vendorscatalogsearch')->isMinQueryLength()) {
                $query->save();
            }
        }
        else {
        	$current_url_key = Mage::getStoreConfig('vendors/vendor_page/url_key');
            $this->_redirect($current_url_key."/".Mage::registry('vendor_id'));
        }
    }
}
