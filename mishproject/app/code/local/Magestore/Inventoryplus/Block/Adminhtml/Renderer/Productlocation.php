<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Supplier Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventoryplus_Block_Adminhtml_Renderer_Productlocation extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {

    public function render(Varien_Object $row) {
        $html = '<input type="text" ';
        $html .= 'name="product_location" ';
        $html .= 'id="location-' . $row->getId() . '" ';
        $html .= 'value="' . $row->getProductLocation() . '"';
        $html .= 'style="width:160px !important"';
        $html .= 'class="input-text"/>';
        return $html;
    }

}
