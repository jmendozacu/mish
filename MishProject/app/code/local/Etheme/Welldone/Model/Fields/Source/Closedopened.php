<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Model_Fields_Source_Closedopened
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'closed','label' => Mage::helper('welldone')->__('closed')),
            array('value'=>'opened','label' => Mage::helper('welldone')->__('opened')),
        );
    }
}
