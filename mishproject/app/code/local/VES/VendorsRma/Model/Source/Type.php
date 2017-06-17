<?php
class VES_VendorsRma_Model_Source_Type extends Mage_Core_Model_Abstract
{
    /** get Type Name by Id */
    public function getTitleById($id){
        $title = null;
        $type = Mage::getModel("vendorsrma/type")->load($id);
        if($type->getId() && $id) $title = $type->getTitle();
        return $title;
    }

}