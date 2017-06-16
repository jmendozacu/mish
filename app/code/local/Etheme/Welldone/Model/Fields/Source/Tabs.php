<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Model_Fields_Source_Tabs
{
    public function toOptionArray()
    {
        return array(
           /* array('value'=>'content','label' => Mage::helper('welldone')->__('1 column')),*/
            array('value'=>'default','label' => Mage::helper('welldone')->__('Default')),
            array('value'=>'sticky','label' => Mage::helper('welldone')->__('Sticky')),
            array('value'=>'0','label' => Mage::helper('welldone')->__('No Tabs')),
            /*array('value'=>'left_right','label' => Mage::helper('welldone')->__('Left+Right Columns')),*/
        );
    }
}
