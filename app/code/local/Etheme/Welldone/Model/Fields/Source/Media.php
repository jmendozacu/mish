<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Model_Fields_Source_Media
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'cloudzoom','label' => Mage::helper('welldone')->__('Cloudzoom')),
            array('value'=>'scroll','label' => Mage::helper('welldone')->__('Scroll')),
        );
    }
}
