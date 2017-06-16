<?php
class Mirasvit_Kb_Block_Tag_Cloud extends Mage_Core_Block_Template
{
    public function getTagCollection()
    {
        $collection = Mage::getModel('kb/tag')->getCollection()
        	->joinNotEmptyFields()
            ->setOrder('ratio')
            ->setPageSize(20);
        return $collection;
    }

    public function getCategory()
    {
        return Mage::registry('current_kbcategory');
    }
}