<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Deliverydate
 * @version    1.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Deliverydate_Block_Frontend_Checkout_Onepage_Deliverydate extends Mage_Core_Block_Template{
	const DATE_FIELD_NAME = "aw_deliverydate_date";
	
	/**
	 * Returns calendar block
	 * @return 
	 */
	public function getCalendarBlock() {
         
        if($date =  Mage::getSingleton('customer/session')->getAwDeliverydateDate()) {           
            $date = Mage::helper('core')->formatDate($date);
        }
        
		if(!$this->getData('calendar_block')) {
			$calendar = $this->getLayout()
	            ->createBlock('deliverydate/html_date')
	            ->setId('id-'.self::DATE_FIELD_NAME)
	            ->setName(self::DATE_FIELD_NAME)
	            ->setClass('product-custom-option datetime-picker input-text')
	            ->setImage(Mage::getDesign()->getSkinUrl('aw_deliverydate/images/grid-cal.gif'))
				->setValue($date)
	            ->setFormat(Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
			$this->setData('calendar_block', $calendar);
		}
		return $this->getData('calendar_block');
	}
	
	/**
	 * Returns ready-to-use calendar field HTML
	 * @return string 
	 */
	public function getCalendarHtml(){
		$this->getFirstAvailableDate();
        return $this->getCalendarBlock()->getHtml();
	}
	
	/**
	 * Returns string
	 * @return string 
	 */
	public function getFormattedTime(){
		$Date = Mage::app()->getLocale()->date();
		$time = array('hour'=>null, 'minute'=>null, 'second'=>null);
		list($time['hour'], $time['minute'], $time['second']) = explode(",", Mage::getStoreConfig(AW_Deliverydate_Helper_Config::XML_PATH_GENERAL_MAX_SAMEDAY_TIME));
		$Date->setTime($time);
		return $this->formatTime(
			$Date
		);
	}

}