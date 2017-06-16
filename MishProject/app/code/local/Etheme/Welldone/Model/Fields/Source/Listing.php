<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Model_Fields_Source_Listing
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'two','label' => Mage::helper('welldone')->__('2')),
            array('value'=>'three','label' => Mage::helper('welldone')->__('3 (2 with opened filter)')),
            array('value'=>'four','label' => Mage::helper('welldone')->__('4 (3 with opened filter)')),
            array('value'=>'five','label' => Mage::helper('welldone')->__('5 (4 with opened filter)')),
            array('value'=>'six','label' => Mage::helper('welldone')->__('6 (5 with opened filter) for full width site')),
            array('value'=>'seven','label' => Mage::helper('welldone')->__('7 (6 with opened filter) for full width site')),
            array('value'=>'eight','label' => Mage::helper('welldone')->__('8 (7 with opened filter) for full width site')),
        );
    }
}
