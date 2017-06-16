<?php 
class VES_VendorsRma_Block_Customer_Account_Message extends VES_VendorsRma_Block_Customer_Account_View
{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	public function getClassHeader(){
		return  'header_op';
	}

    public function getMessage(){
        $messages=Mage::getModel('vendorsrma/message')->getCollection()->addFieldToFilter('request_id',$this->getRequestRma()->getId())->setOrder('created_time', 'ASC');
        return $messages;
    }


    public function isHtmlMessage($message){
        return ($message != strip_tags($message));
    }

    

    public function getNameAttachment($file){
        $files = explode("/",$file);
        return $files[count($files)-1];
    }
    
    

    public function getClassType($message){
        $type = $message->getData('type');
        $class = "";
        switch($type){
            case VES_VendorsRma_Model_Source_Message_Type::TYPE_VENDOR:
                $class = "message-box-department";
                break;
            case VES_VendorsRma_Model_Source_Message_Type::TYPE_ADMIN:
                $class = "message-box-admin";
                break;
            case VES_VendorsRma_Model_Source_Message_Type::TYPE_CUSTOMER:
                $class = "message-box-customer";
                break;
        }
        return $class;
    }
}