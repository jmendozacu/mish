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

class AW_Deliverydate_Block_Html_Date extends Mage_Core_Block_Html_Date{

	public function getRecurrentWeekdaysJson($weekdays){
		return Zend_Json::encode($weekdays);
	}
	
	public function getRecurrentDaysJson($days){
		return Zend_Json::encode($days);
	}
	
	public function getDaysJson($arr){
		$dates = array();
		foreach($arr as $Date){
			$dates[] = $Date->toString(AW_Core_Model_Abstract::JS_DATE_FORMAT);
		}
		return Zend_Json::encode($dates);
	}	


    protected function _toHtml(){
    	
		$Days = $this->getUnavailDays();
		$first_day = $this->getFirstAvailableDate()->toArray();
   	
        $html  = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'value="'.$this->getValue().'" class="'.$this->getClass().'"  '.$this->getExtraParams().'/> ';

        $html .= '<img src="' . $this->getImage() . '" alt="" class="v-middle" ';
        $html .= 'title="' . $this->helper('core')->__('Select Date') . '" id="' . $this->getId() . '_trig" />';

        $html .=
	
        '<script type="text/javascript">
			
			AW_DD_Days = '.$this->getDaysJson($Days->getDays()).'
			AW_DD_RWeekdays = '.$this->getRecurrentWeekdaysJson($Days->getRecurrentWeekdays()).'
			AW_DD_RDays = '.$this->getRecurrentDaysJson($Days->getRecurrentDays()).'
			T = new Date;
			T.setDate('.$first_day['day'].')
			T.setFullYear('.$first_day['year'].')
			T.setMonth('.($first_day['month']-1).')
			T.setHours(0);
			T.setMinutes(0);
			T.setSeconds(0);
			T.setMilliseconds(0);
		
			Calendar.setup({
                inputField  : "' . $this->getId() . '",
                ifFormat    : "' . $this->getFormat() . '",
                button      : "' . $this->getId() . '_trig",
                align       : "Bl",
                singleClick : true,
				
				disableFunc : function(){
					D = arguments[0];
					D.setHours(0);
					D.setMinutes(0);
					D.setSeconds(0);
					D.setMilliseconds(0);
					
					if(D.getTime() < T.getTime()){
						return true;
					}
					
					dateFormatted = D.getFullYear()+"-"+(D.getMonth()+1)+"-"+D.getDate();
					
					weekDay = D.getDay();
					monthDay = D.getDate();
					 					
					if(
						(AW_DD_RDays.indexOf(monthDay) != -1) ||
						(AW_DD_RWeekdays.indexOf(weekDay) != -1) ||
						(AW_DD_Days.indexOf(dateFormatted) != -1)){
						
						return true;
					}
					return false;
				}
            });
			try{
				$("opc-shipping_method").style.position = "static";
			}catch(e){}
        </script>';


        return $html;
    }

	/**
	* Get uanavailable dates
	*
	* @param Mage_Catalog_Model_Product $product affected product
	* @return array binded dates timestamps
	*/
	public function getUnavailDays(){
		if(!$this->getData('unavail_days')){
			Zend_Date::setOptions(array('extend_month' => true)); // Fix Zend_Date::addMonth unexpected result 
			
			$r_weekdays = array(); // Recurrent weekdays
			$r_dates = array(); //Recurrent days of months
			$periods = array();
			$common = array();
			
			$out = array();
			
			/* We should also test for excluded days */
			
			// Get all "single days"
			$rules = Mage::getModel('deliverydate/holiday')->getCollection();
			foreach($rules as $rule){
				$from = new Zend_Date($rule->getPeriodFrom(), AW_Core_Model_Abstract::DB_DATE_FORMAT);
				$to = new Zend_Date($rule->getPeriodTo(), AW_Core_Model_Abstract::DB_DATE_FORMAT);
				
				switch($rule->getPeriodType()){
					case 'single':
						$common[] = $from;
					break;
					case 'recurrent_day':
						// Recurrent day of week
						$arr = $from->toArray();
						$weekday = $arr['weekday'] == 7 ? 0 : (int)$arr['weekday'] ;
						$r_weekdays[] = $weekday;
	
					break;	
					case 'recurrent_date':
						$arr = $from->toArray();
						$day = $arr['day'];
						$r_dates[] = (int)$day;
					break;	
					case 'period':
						// Period
						while($from->compare($to) <= 0 ){
							$common[] = clone $from;
							$from = $from->addDayOfYear(1);
						}
					break;
								
					case 'recurrent_period':
						throw new Exception("Reccurent period is not supported");
						// Recurring Period
						$type = $rule->getPeriodRecurrenceType();
						
						for($_ts = strtotime($product->getAwBookingDateFrom()); $_ts<=strtotime($product->getAwBookingDateTo()); $_ts += self::ONE_DAY){
							if($type == 'monthly'){
								// Monthly repeated date period
								$date_ts = date('d', $_ts);
								$date_from = date('d', $ts_from);
								$date_to = date('d', $ts_to);
								
								if(in_array($date_ts, range($date_from, $date_to))){
									$out[] = $_ts;
								}
							}else{
								// Yearly recurrent period
								list($y_ts, $m_ts, $d_ts) = explode('-',date('Y-m-d', $_ts));
								list($m_from, $d_from) = explode('-',date('m-d', $ts_from));
								list($m_to, $d_to) = explode('-',date('m-d', $ts_to));
								
								$time_from = mktime(0,0,1, $m_from, $d_from, $y_ts);
								$time_to = mktime(0,0,1, $m_to, $d_to, $y_ts);
								if($_ts >= $time_from && $_ts <= $time_to){
									$out[] = $_ts;
								}
							}
							
							
						}
						
						for($_ts = $ts_from; $_ts<=$ts_to; $_ts += self::ONE_DAY){
							$out[] = $_ts;
						}
					break;					
				}
			}
			
			$data = new Varien_Object(array(
				'recurrent_weekdays' => $r_weekdays,
				'recurrent_days' => $r_dates,
				'days' => $common
			));
			
			$this->setData('unavail_days', $data);
		}
		return $this->getData('unavail_days');
	}
	
	/**
	 * Checks if date is available
	 * @param Zend_Date $Date
	 * @return 
	 */
	public function isDateAvail(Zend_Date $Date){
		$unavail = $this->getUnavailDays()->getData();
		$data = $Date->toArray();
		$weekday = $data['weekday'] == 7 ? 0 : (int)$data['weekday'] ;
		
		
		if(
			(array_search($weekday, $unavail['recurrent_weekdays']) !== false) ||
			(array_search($data['day'], $unavail['recurrent_days']) !== false) 
		){
			
			return false;
		}
		
		foreach($unavail['days'] as $Day){
			if($Day->compare($Date, Zend_Date::DATE_SHORT) == 0){
				return false;
			}
		}
		return true;
	}

	
	/**
	 * Returns first availavle date
	 * @return Zend_Date 
	 */
	public function getFirstAvailableDate(){
		Zend_Date::setOptions(array('extend_month' => true)); // Fix Zend_Date::addMonth unexpected result 
		$Date = Mage::app()->getLocale()->date();
		// 1. Check if time is less than allowed 
		$Latest = Mage::app()->getLocale()->date();
		$time = array('hour'=>null, 'minute'=>null, 'second'=>null);
		list($time['hour'], $time['minute'], $time['second']) = explode(",", Mage::getStoreConfig(AW_Deliverydate_Helper_Config::XML_PATH_GENERAL_MAX_SAMEDAY_TIME));
		
		$Latest->setTime($time);
		$time_shift = $date_shift = 0;
		
		if($Date->compare($Latest) >= 0 ){
			// Time expired. Add one day
			$Date = $Date->addDayOfYear(1);
			$time_shift = 1;
			
		}
		
		// 2. If order offset is set, iterate date to the days offset
		if(Mage::getStoreConfig(AW_Deliverydate_Helper_Config::XML_PATH_GENERAL_MIN_DELIVERY_INTERVAL)){
			$Date = $Date->addDayOfYear(Mage::getStoreConfig(AW_Deliverydate_Helper_Config::XML_PATH_GENERAL_MIN_DELIVERY_INTERVAL));
			$date_shift = 1;
		}
		
		// 3. Iterate a day while it will not be allowed
		while(!$this->isDateAvail($Date)){
			$Date = $Date->addDayOfYear(1);
			$date_shift = 1;
		}
		
		if($date_shift && $time_shift){
			//$Date = $Date->addDayOfYear(-1);
		}
		return $Date;
	}

	public function getDefaultValue(){
		return $this->formatDate($this->getFirstAvailableDate());
	}

	/**
	 * Override getValue to return default value
	 * @return 
	 */
	public function getValue(){
		if(!($value = $this->getData('value'))){
			return $this->getDefaultValue();
		}
		return $value;
	}

}
