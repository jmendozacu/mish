<?php
class VES_VendorsRma_Model_Source_Status extends Mage_Core_Model_Abstract
{

    /** get Status Name by Id */
    public function getTitleById($id){
        $title = null;
        $type = Mage::getModel("vendorsrma/status")->load($id);
        if($type->getId() && $id) $title = $type->getTitle();
        return $title;
    }

}