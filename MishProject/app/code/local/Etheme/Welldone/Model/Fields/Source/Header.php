<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Model_Fields_Source_Header
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'1','label' => Mage::helper('welldone')->__('Type 1 (Default)')),
            array('value'=>'2','label' => Mage::helper('welldone')->__('Type 2 (Short+Hidden Menu)')),
            array('value'=>'3','label' => Mage::helper('welldone')->__('Type 3 (Transparent+Slider Required)')),
            array('value'=>'4','label' => Mage::helper('welldone')->__('Type 4 (Maximum)')),
        );
    }
}
