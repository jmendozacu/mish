<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Model_Fields_Source_Font
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'custom', 'label' => Mage::helper('welldone')->__('Custom+')),
            array('value' => 'google', 'label' => Mage::helper('welldone')->__('Google Fonts+')),
            array('value' => 'Arial, "Helvetica Neue", Helvetica, sans-serif', 'label' => Mage::helper('welldone')->__('Arial, "Helvetica Neue", Helvetica, sans-serif')),
            array('value' => 'Georgia, serif',  'label' => Mage::helper('welldone')->__('Georgia, serif')),
            array('value' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif', 'label' => Mage::helper('welldone')->__('"Lucida Sans Unicode", "Lucida Grande", sans-serif')),
            array('value' => 'Tahoma, Geneva, sans-serif',  'label' => Mage::helper('welldone')->__('Tahoma, Geneva, sans-serif')),
            array('value' => '"Trebuchet MS", Helvetica, sans-serif',  'label' => Mage::helper('welldone')->__('"Trebuchet MS", Helvetica, sans-serif')),
            array('value' => 'Verdana, Geneva, sans-serif', 'label' => Mage::helper('welldone')->__('Verdana, Geneva, sans-serif')),
        );
    }
}
