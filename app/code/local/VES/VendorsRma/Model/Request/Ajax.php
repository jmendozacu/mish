<?php
class VES_VendorsRma_Model_Request_Ajax extends Varien_Object
{
    /** text json ajax update attribute */

    public function convertText($node,$value,$id){
        $time= now();
        $rma = Mage::getModel('vendorsrma/request');
        $model=Mage::getModel('vendorsrma/request')->load($id);
        $check = false;
        switch ($node){
            case 'status':
                $html = Mage::getModel('vendorsrma/source_status')->getTitleById($value);
                if($model->getStatus() != $value) $check = true;
                break;
            case 'type':
                $html = Mage::getModel('vendorsrma/source_type')->getTitleById($value);
                break;
            case 'reason':
                $html = Mage::getModel('vendorsrma/source_reason')->getTitleById($value);
                break;
            case 'package_opened':
                $html= Mage::getModel('vendorsrma/option_pack')->getTitleByKey($value);
                break;
            case 'state':
                  $html= Mage::getModel('vendorsrma/option_state')->getTitleByKey($value,5);
                  $model->setData("flag_state",5);
                  break;
            case 'tracking_code':
                $html= $value;
                break;
            default:
                $html= $value;
        }
        $model->setData($node,$value)->save();
        if($check){
            if (!Mage::app()->getStore()->isAdmin()) {
            	$model->saveNotify();
                $vendor = Mage::getModel("vendors/vendor")->load($model->getVendorId());
                $model->saveStatusHistory(VES_VendorsRma_Model_Option_Status_Type::TYPE_VENDOR,$vendor->getTitle());
            }
            else{
            	$model->saveNotify("admin");
                $model->saveStatusHistory(VES_VendorsRma_Model_Option_Status_Type::TYPE_ADMIN,Mage::helper("vendorsrma")->__("Administrator"));
            }
        }
        return $html;
    }
}