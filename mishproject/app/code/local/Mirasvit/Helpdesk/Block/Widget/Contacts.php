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



class Mirasvit_Helpdesk_Block_Widget_Contacts extends Mirasvit_Helpdesk_Block_Contacts_Form implements Mage_Widget_Block_Interface
{
	public function __construct() {
		$this->setTemplate('mst_helpdesk/contacts/form.phtml');
		parent::__construct();
	}
}
