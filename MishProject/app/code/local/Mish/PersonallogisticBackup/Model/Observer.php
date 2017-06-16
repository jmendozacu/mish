<?php

class Mish_Personallogistic_Model_Observer 
{

    public function personallogisticApproval($observer)
     {
          $status     = $observer->getEvent()->getData('status');
          $personalLogisticIds   = $observer->getEvent()->getData('id');
          foreach ($personalLogisticIds as $personalLogisticId) {
          	$personalLModel = Mage::getModel('personallogistic/personallogistic')->load($personalLogisticId);
          	  $plemail = $personalLModel->getMail();
          	  $plfirstname  = $personalLModel->getFirstname();
          	  $pllastname  = $personalLModel->getLastname();
          	  $plfullname = $plfirstname." ".$pllastname;

      	   // $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^^&*()';
          //  $pass = array(); //remember to declare $pass as an array
          //  $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
          //  for ($i = 0; $i < 10; $i++) {
          //      $n = rand(0, $alphaLength);
          //      $pass[] = $alphabet[$n];
          //     }
          //  $password = implode($pass);
          	  $this->pLogisticapprovalsendEmail($plemail,$plfullname);
          }
        

       

     }

 /********** send email starts here ************/

    public function pLogisticapprovalsendEmail($plemail,$plfullname)
    {

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);         
        $mailTemplate = Mage::getModel('core/email_template')->loadDefault('personallogisticapprovedemail');
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
 
        $vars = array('pl_email'=>$plemail,
        	          'pl_name' =>$plfullname,
        	          /*'password'=>$password,*/
                      );

              
        $storeId = Mage::app()->getStore()->getId();
        $model   = $mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject);
        $model  -> sendTransactional($mailTemplate,$sender,$plemail, $plfullname, $vars, $storeId);
         
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
    
    
