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

class MageWorx_DeliveryZone_Block_Adminhtml_Rates extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_addButton('export', array(
            'label'     => $this->__('Export Rates to CSV'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/exportRates') .'\')',
            'class'     => 'export',
        ));
        $this->_addButton('import', array(
            'label'     => $this->__('Import Rates'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/import') .'\')',
            'class'     => 'import',
        ));
        $this->_blockGroup = 'deliveryzone';
        $this->_controller = 'adminhtml_rates';
        $this->_headerText = Mage::helper('deliveryzone')->__('Shipping Suite Rules');
        $this->_addButtonLabel = Mage::helper('deliveryzone')->__('Add New Rule');
        parent::__construct();
             // Need for initialize DHL shipping method!
            if(!Mage::getStoreConfig('carriers/dhlint/content_type')) {
                $model = Mage::getModel('core/config');
                $model->saveConfig('carriers/dhlint/content_type','D');
            }
    }
}
