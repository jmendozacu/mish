<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Model_Fields_Source_Productpage
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'default','label' => Mage::helper('welldone')->__('Default')),
            array('value'=>'sticky','label' => Mage::helper('welldone')->__('Sticky')),
        );
    }
}
