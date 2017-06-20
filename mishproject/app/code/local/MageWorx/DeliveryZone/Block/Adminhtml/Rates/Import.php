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

class MageWorx_DeliveryZone_Block_Adminhtml_Rates_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
     public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'deliveryzone';
        $this->_controller = 'adminhtml_rates';
        $this->_mode = 'import';
        $this->_addButton('import', array(
                'label' => Mage::helper('customer')->__('Import'),
                'onclick'   => 'editForm.submit();',
                'class' => 'add',
            ), 0);

        parent::__construct();
        
        $this->_removeButton('save');
        $this->_removeButton('reset');
    }
    
    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('deliveryzone')->__('Import Shipping Suite Rules');
    }
}