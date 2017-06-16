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

class MageWorx_DeliveryZone_Model_Mysql4_Zone_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('deliveryzone/zone');
    }
    
    public function addCalculation() {
        $this->getSelect()
                ->joinLeft(array('country'=>$this->getTable('deliveryzone/zone_location')), 'main_table.zone_id=country.zone_id',array('countries'=>'COUNT(DISTINCT country.country_id)'))
                ->joinLeft(array('category'=>$this->getTable('deliveryzone/zone_category')), 'main_table.zone_id=category.zone_id',array('categories'=>'COUNT(DISTINCT category.category_id)'))
                ->joinLeft(array('product'=>$this->getTable('deliveryzone/zone_product')), 'main_table.zone_id=product.zone_id',array('products'=>'COUNT(DISTINCT product.product_id)'))
                ->group('main_table.zone_id')
        ;
        return $this;
    }
}