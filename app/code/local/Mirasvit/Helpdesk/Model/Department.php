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


class Mirasvit_Helpdesk_Model_Department extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('helpdesk/department');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }
 
    public function getName()
    {
        return Mage::helper('helpdesk/storeview')->getStoreViewValue($this, 'name');
    }

    public function setName($value)
    {
        Mage::helper('helpdesk/storeview')->setStoreViewValue($this, 'name', $value);
        return $this;
    }

    public function getNotificationEmail()
    {
        return Mage::helper('helpdesk/storeview')->getStoreViewValue($this, 'notification_email');
    }

    public function setNotificationEmail($value)
    {
        Mage::helper('helpdesk/storeview')->setStoreViewValue($this, 'notification_email', $value);
        return $this;
    }

    public function addData(array $data)
    {
        if (isset($data['name']) && strpos($data['name'], 'a:') !== 0) {
            $this->setName($data['name']);
            unset($data['name']);
        }

        if (isset($data['notification_email']) && strpos($data['notification_email'], 'a:') !== 0) {
            $this->setNotificationEmail($data['notification_email']);
            unset($data['notification_email']);
        }

        return parent::addData($data);
    }
	/************************/

    public function getUsers() {
        if (!$this->getUserIds()) {
            $this->getResource()->loadUserIds($this);
        }

        return Mage::getModel('admin/user')->getCollection()
            ->addFieldToFilter('user_id', $this->getUserIds())
            ->addFieldToFilter('is_active', true);
    }

    public function __toString()
    {
        return $this->getName();
    }
}
