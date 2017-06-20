<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Model_Fields_Source_Sitewidth
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'0','label' => Mage::helper('welldone')->__('Default 1170px')),
            array('value'=>'1','label' => Mage::helper('welldone')->__('Boxed (not for Landing Content)')),
            array('value'=>'2','label' => Mage::helper('welldone')->__('Fullwidth')),
            /*array('value'=>'3','label' => Mage::helper('welldone')->__('Wide 1770px')),*/
        );
    }
}
