<?php
class VES_VendorsRma_Model_Source_Reason extends Mage_Core_Model_Abstract
{

    /** get Reason Name by Id */
    public function getTitleById($id){
        $title = null;
        $type = Mage::getModel("vendorsrma/reason")->load($id);
        if($type->getId() && $id) $title = $type->getTitle();
        return $title;
    }

}

