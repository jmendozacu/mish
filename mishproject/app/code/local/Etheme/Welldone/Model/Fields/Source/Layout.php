<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Welldone_Model_Fields_Source_Layout
{
    public function toOptionArray()
    {
        return array(
           /* array('value'=>'content','label' => Mage::helper('welldone')->__('1 column')),*/
            array('value'=>'left','label' => Mage::helper('welldone')->__('Left Column')),
            array('value'=>'right','label' => Mage::helper('welldone')->__('Right Column')),
            /*array('value'=>'left_right','label' => Mage::helper('welldone')->__('Left+Right Columns')),*/
        );
    }
}
