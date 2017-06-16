<?php

/**
 * Adminhtml grid item renderer
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */

class VES_VendorsRma_Block_Adminhtml_Widget_Grid_Renderer_State
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
	/**
     * Render a grid cell as options
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        $showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
        
        $id =  $row->getData("entity_id");
        $rma = Mage::getModel('vendorsrma/request')->load($id);
        $flag_state = $rma->getData("flag_state");
        if (!empty($options) && is_array($options)) {
            $value = $row->getData($this->getColumn()->getIndex());
            if (is_array($value)) {
                $res = array();
                foreach ($value as $item) {
                    if (isset($options[$item])) {
                        $res[] = $this->escapeHtml($options[$item]);
                    }
                    elseif ($showMissingOptionValues) {
                        $res[] = $this->escapeHtml($item);
                    }
                }
                return implode(', ', $res);
            } elseif (isset($options[$value])) {
                $text =  Mage::getModel('vendorsrma/option_state')->getTitleByKey($value,$rma->getData('flag_state'));
                return $this->escapeHtml($text);
            } elseif (in_array($value, $options)) {
                return $this->escapeHtml($value);
            }
        }
    }
}
