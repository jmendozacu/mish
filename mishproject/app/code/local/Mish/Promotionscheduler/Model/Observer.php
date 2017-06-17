<?php

class Mish_Promotionscheduler_Model_Observer
{
    public function promotion()
    {

    	$enable = Mage::getStoreConfig('promotionscheduler/general/enable');
    	$receivermail = Mage::getStoreConfig('promotionscheduler/general/receivermail');
    	$receivername = Mage::getStoreConfig('promotionscheduler/general/receivername');
    	$days = Mage::getStoreConfig('promotionscheduler/general/days');

    	$promoModel = Mage::getModel('salesrule/rule')->getCollection();

    	$currentDate  = date('Y-m-d');
    	

    	foreach ($promoModel as $promotionData) {
    		$name = $promotionData['name'];
    		$to_date = $promotionData['to_date'];
    		$date = date('Y-m-d', strtotime($to_date . ' -'.$days.' days'));
    		if($date == $currentDate){
                $promoname = $promotionData['name'];
                $todate = $promotionData['to_date'];
    		 $this->promotionMailExpire($receivermail,$receivername,$date,$promoname,$todate);
    		}
    	}
    }

     public function promotionMailExpire($receivermail,$receivername,$date,$promoname,$todate)
    {

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);         
        $mailTemplate = Mage::getModel('core/email_template')->loadDefault('promotionExpiryMail');
        $template_collection =  $mailTemplate->load($templateId);
        $template_data       = $template_collection->getData();
        if(!empty($template_data))
        {
            $templateId  = $template_data['template_id'];
            $mailSubject = $template_data['template_subject'];                         
        $from_email = Mage::getStoreConfig('trans_email/ident_sales/email'); //fetch sender email
        $from_name  = Mage::getStoreConfig('trans_email/ident_sales/name'); //fetch sender name
 
        $sender = array('name'  => $from_name,
                        'email' => $from_email);                                
 
        $vars = array('receiver_email'=>$receivermail,
        	          'receiver_name' =>$receivername,
                      'promotionname' =>$promoname,
                      'todate'=>$todate,
        	         
                      );

              
        $storeId = Mage::app()->getStore()->getId();
        $model   = $mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject);
        $model  -> sendTransactional($mailTemplate,$sender,$receivermail, $receivername, $vars, $storeId);
         
        if (!$mailTemplate->getSentSuccess()) {
                //throw new Exception();
                return false;
        }
        $translate->setTranslateInline(true);
        return true;
    }           
}
 /********** Send email end here ************/
}