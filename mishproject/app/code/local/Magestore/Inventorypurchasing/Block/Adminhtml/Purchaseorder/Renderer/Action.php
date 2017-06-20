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
 * Inventory Supplier Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */
class Magestore_Inventorypurchasing_Block_Adminhtml_Purchaseorder_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        $purchase_order_id = $row->getPurchaseOrderId();
        $status = $row->getStatus();
        $content = '';
        $url_edit = Mage::helper('adminhtml')->getUrl('*/*/edit', array('id'=>$purchase_order_id));
        $url_move = Mage::helper('adminhtml')->getUrl('*/*/movetotrash', array('id'=>$purchase_order_id));
        if(in_array($status, array(Magestore_Inventorypurchasing_Model_Purchaseorder::AWAITING_DELIVERY_STATUS,
                                    Magestore_Inventorypurchasing_Model_Purchaseorder::RECEIVING_STATUS))){
            $content .= "<div>
                            <div style='float:left;'>
                                <a href=".$url_edit.">
                                    <span style='margin-right:10px; text-align:center;'>".Mage::helper('inventorypurchasing')->__('Edit')."</span>
                                </a>
                            </div>
                        </div>";
        } else {
            $content .= "<div style='width:130px;'>
                            <div style='float:left;'>
                                <a href=".$url_edit.">
                                    <span style='margin-right:10px;'>".Mage::helper('inventorypurchasing')->__('Edit')."</span>
                                </a>
                            </div>";
            $content .=     "<div class='trash_disable' style='float:left;'>|</div>";
            $content .=     "<div class='trash_disable' style='float:left;'>
                            <a href=" .$url_move. ">
                                <span style='margin-left:10px;'>".Mage::helper('inventorypurchasing')->__('MoveToTrash')."</span>
                                </a>
                            </div>
                            <br style='clear:both;'/>
                        </div>";
        }
        return $content;
    }

}
