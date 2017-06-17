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


class Mirasvit_Helpdesk_Model_Draft extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('helpdesk/draft');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }

	/************************/

    public function getUsersOnline()
    {
        $value = $this->getData('users_online');
        if (is_array($value)) {
            return $value;
        }
        $value = unserialize($value);
        $this->setData('users_online', $value);
        return $value;
    }

    protected $_user = null;
    public function getUser()
    {
        if (!$this->getUpdatedBy()) {
            return false;
        }
    	if ($this->_user === null) {
            $this->_user = Mage::getModel('admin/user')->load($this->getUpdatedBy());
    	}
    	return $this->_user;
    }

    public function getDraftStatusMessage()
    {
    	if ($this->getBody() == '') {
    		return false;
    	}
	    $userName = '';
	    if ($user = $this->getUser()) {
	    	$userName = $user->getName();
	    }
	    $time = Mage::helper('helpdesk/string')->nicetime(strtotime($this->getUpdatedAt()));
        $draftMessage = Mage::helper('helpdesk')->__("Edited by %s (%s)", $userName, $time);
        return $draftMessage;
    }
}