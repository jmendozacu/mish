<?php

class Ves_Tabs_Model_System_Config_Source_ListType
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'news', 'label'=>Mage::helper('ves_tabs')->__('News Arrival')),
            array('value'=>'latest', 'label'=>Mage::helper('ves_tabs')->__('Latest')),
            array('value'=>'bestseller', 'label'=>Mage::helper('ves_tabs')->__('Best Seller')),
            array('value'=>'mostviewed', 'label'=>Mage::helper('ves_tabs')->__('Most Viewed')),
            array('value'=>'special', 'label'=>Mage::helper('ves_tabs')->__('Special')),
            array('value'=>'featured', 'label'=>Mage::helper('ves_tabs')->__('Featured Product'))
        );
    }    
}
