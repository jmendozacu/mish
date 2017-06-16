<?php
/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_AdvancedStockBar
 * @version    1.0.0
 * @copyright  Copyright (c) 2012 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */
class Magecon_AdvancedStockBar_Block_Threshold extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

    public function __construct() {
        $this->_prepareToRender();
        parent::__construct();
    }

    public function _prepareToRender() {

        $this->addColumn('threshold_value', array(
            'label' => Mage::helper('advancedstockbar')->__('Threshold'),
            'style' => 'width:100px',
        ));

        $this->addColumn('status', array(
            'label' => Mage::helper('advancedstockbar')->__('Availability Status'),
            'style' => 'width:300px',
        ));

        $this->addColumn('color', array(
            'label' => Mage::helper('advancedstockbar')->__('Status Color (hex starting with #)'),
            'style' => 'width:100px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('advancedstockbar')->__('Add Threshold');
    }

}
?>