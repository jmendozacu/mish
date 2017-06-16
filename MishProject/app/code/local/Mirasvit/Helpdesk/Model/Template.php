<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Help Desk MX
 * @version   1.1.0
 * @build     1285
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Helpdesk_Model_Template extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('helpdesk/template');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }


	/************************/

    public function getParsedTemplate($ticket)
    {
    	$storeId = $ticket->getStoreId();
        $storeOb = Mage::getModel('core/store')->load($storeId);
        if (!$name = Mage::getStoreConfig('general/store_information/name', $storeId)) {
            $name = $storeOb->getName();
        }
        $store = new Varien_Object(array(
            'name' => $name,
            'phone' => Mage::getStoreConfig('general/store_information/phone', $storeId),
            'address' => Mage::getStoreConfig('general/store_information/address', $storeId),
        ));
        $user = Mage::getSingleton('admin/session')->getUser();
        $result = Mage::helper('mstcore/parsevariables')->parse($this->getTemplate(), array(
            'ticket' => $ticket,
            'store'  => $store,
            'user'  => $user
            ),
            array(), $store->getId());

        return $result;
    }
}