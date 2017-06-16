<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx DeliveryZone extension
 *
 * @category   MageWorx
 * @package    MageWorx_DeliveryZone
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

/**
* Our test shipping method module adapter
*/
class MageWorx_DeliveryZone_Model_Carrier_Deliveryzone extends Mage_Shipping_Model_Carrier_Abstract 
implements Mage_Shipping_Model_Carrier_Interface
{
  /**
   * unique internal shipping method identifier
   *
   * @var string
   */
    protected $_code = 'deliveryzone';
 
  /**
   * Collect rates for this shipping method based on information in $request
   *
   * @param Mage_Shipping_Model_Rate_Request $data
   * @return Mage_Shipping_Model_Rate_Result
   */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
            return false;
        }
        $result = Mage::getModel('shipping/rate_result');
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setMethod($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethodTitle($this->getConfigData('name'));
        $method->setPrice($this->getConfigData('price'));
        $method->setCost($this->getConfigData('price'));
        $result->append($method);
        return $result;
    }
    
    /**
     * Is Method Allowed
     * @return array
     */
    public function getAllowedMethods()
    {
        return array('deliveryzone'=>$this->getConfigData('name'));
    }
}