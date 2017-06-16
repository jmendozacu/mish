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

class MageWorx_DeliveryZone_Model_Mysql4_Zone_Location_Collection extends MageWorx_DeliveryZone_Model_Mysql4_Zone_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('deliveryzone/zone_location');
    }
    
    public function loadZoneByLocation($countryId) {
        $this->getSelect()
                ->where("country_id=?",$countryId)
         ->joinInner(array('zone'=>$this->getTable('zone')), 'main_table.zone_id=zone.zone_id', "")
                ->where("zone.status=1");
        return $this;
    }
    
    public function loadByProductId($productId) {
        $this->getSelect()
                ->joinLeft(array('p'=>$this->getTable('zone_product')), 'main_table.zone_id=p.zone_id', "")
                ->joinInner(array('zone'=>$this->getTable('zone')), 'main_table.zone_id=zone.zone_id', "")
                ->where("zone.status=1")
                ->where("p.product_id =?",$productId);
        return $this;
    }
    public function loadByProductIds($productIds) {
        $this->getSelect()
                ->joinLeft(array('p'=>$this->getTable('zone_product')), 'main_table.zone_id=p.zone_id', "")
                ->joinInner(array('zone'=>$this->getTable('zone')), 'main_table.zone_id=zone.zone_id', "")
                ->where("zone.status=1")
                ->where("p.product_id IN(?)",$productIds);
        return $this;
    }
    
    public function joinCarriers() {
        $this->getSelect()
                ->joinLeft(array('carrier'=>$this->getTable('zone_shippingmethod')), 'main_table.zone_id=carrier.zone_id', "allowed_methods");
        return $this;
    }
    
    public function getCarriersByLocation($countryId) {
        $this->getSelect()->reset()
                ->join(array('main_table'=>$this->getTable('zone_location')), '', "")
                ->joinInner(array('zone'=>$this->getTable('zone')), 'main_table.zone_id=zone.zone_id', "")
                ->where("zone.status=1")
                ->joinLeft(array('carrier'=>$this->getTable('zone_shippingmethod')), 'main_table.zone_id=carrier.zone_id', "*")
                ->where("country_id=?",$countryId);
        return $this;
    }
}