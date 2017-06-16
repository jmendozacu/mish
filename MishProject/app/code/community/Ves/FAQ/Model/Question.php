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
class Ves_FAQ_Model_Question extends Mage_Core_Model_Abstract
{
	const STATUS_APPROVED = 1;
	const STATUS_PENDING = 2;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('ves_faq/question');
    }

	/**
     * This method get list status
     */
	public function getListStatus(){
		return array(
			self::STATUS_APPROVED => Mage::helper('ves_faq')->__('Approved'),
			self::STATUS_PENDING => Mage::helper('ves_faq')->__('Pending'),
			);
	}
}