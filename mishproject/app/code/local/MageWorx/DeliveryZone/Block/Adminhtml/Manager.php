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

class MageWorx_Deliveryzone_Block_Adminhtml_Manager extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
            parent::__construct();
		$this->_blockGroup     = 'deliveryzone';
		$this->_controller     = 'adminhtml_manager';
		$this->_headerText     = Mage::helper('deliveryzone')->__('Manage Shipping Zones');
		$this->_addButtonLabel = Mage::helper('deliveryzone')->__('Add Zone');
                
            // Need for initialize DHL shipping method!
            if(!Mage::getStoreConfig('carriers/dhlint/content_type')) {
                $model = Mage::getModel('core/config');
                $model->saveConfig('carriers/dhlint/content_type','D');
            }
		
	}
}