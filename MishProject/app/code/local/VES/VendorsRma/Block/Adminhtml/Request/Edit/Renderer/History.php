<?php
class VES_VendorsRma_Block_Adminhtml_Request_Edit_Renderer_History extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
    public function __construct()
    {
        $this->setTemplate('ves_vendorsrma/request/history.phtml');
    }
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }


    public function getValue($value = null){
        if($value) return Mage::registry('current_request')->getData($value);
        return Mage::registry('current_request')->getData();
    }
    public function getRequestStatusChange(){
        $id=$this->getValue('entity_id');
        $model = Mage::getModel('vendorsrma/history')->getCollection()->addFieldToFilter('request_id',array('eq'=>$id));
        return $model;
    }
    public function getPrefixChangeBy($type){
        if(!$type) throw new Mage_Core_Exception('The type is not exist');
        switch ($type){
            case VES_VendorsRma_Model_Option_Status_Type::TYPE_VENDOR:
                $prefix="[V]";
                break;
            case VES_VendorsRma_Model_Option_Status_Type::TYPE_CUSTOMER:
                $prefix="[C]";
                break;
            case VES_VendorsRma_Model_Option_Status_Type::TYPE_ADMIN:
                $prefix="[A]";
                break;
        }
        return $prefix;
    }

}


