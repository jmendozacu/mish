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
 * Inventory Supplier Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventory
 * @author      Magestore Developer
 */

class Magestore_Inventorypurchasing_Block_Adminhtml_Purchaseorder_Edit_Tab_Renderer_Purchasestatus
	extends Varien_Data_Form_Element_Select
{
    /* Michael 201602 */
    public function getElementHtml()
    {
        $value = $this->getValue();
        if(!$value)
            $value = Magestore_Inventorypurchasing_Model_Purchaseorder::PENDING_STATUS;
        $labels = Mage::helper('inventorypurchasing/purchaseorder')->getReturnOrderStatus();
        $html = '<strong>';
        $html .= $labels[$value];
        $html.= '</strong>';
        return $html;
    }

    /* old function before 201602 */
    public function getElementHtmlOld()
    {
        $value = $this->getValue();

        if($value && $value!=5 && $value!=6){
            $labels = Mage::helper('inventorypurchasing/purchaseorder')->getReturnOrderStatus();
            $html = '<strong>';
            $html .= $labels[$value];
            $html.= '</strong>';
            return $html;
        }else{
            $this->addClass('select');
            $html = '<select id="'.$this->getHtmlId().'" name="'.$this->getName().'" '.$this->serialize($this->getHtmlAttributes()).'>'."\n";



            if (!is_array($value)) {
                $value = array($value);
            }

            if ($values = $this->getValues()) {
                foreach ($values as $key => $option) {
                    if (!is_array($option)) {
                        $html.= $this->_optionToHtml(array(
                            'value' => $key,
                            'label' => $option),
                            $value
                        );
                    }
                    elseif (is_array($option['value'])) {
                        $html.='<optgroup label="'.$option['label'].'">'."\n";
                        foreach ($option['value'] as $groupItem) {
                            $html.= $this->_optionToHtml($groupItem, $value);
                        }
                        $html.='</optgroup>'."\n";
                    }
                    else {
                        $html.= $this->_optionToHtml($option, $value);
                    }
                }
            }

            $html.= '</select>'."\n";
            $html.= $this->getAfterElementHtml();
            return $html;
        }
    }
}