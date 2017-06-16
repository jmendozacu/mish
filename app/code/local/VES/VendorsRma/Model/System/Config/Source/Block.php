<?php
class VES_VendorsRma_Model_System_Config_Source_Block
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {

        $blocks = array();
        $collection = Mage::getModel('cms/block')->getCollection()->addFieldToFilter("is_active", '1');
        foreach($collection as $block){
            $blocks[] =array('value' => $block->getId() , "label" => $block->getTitle());
        }
        return $blocks;

    }


}
