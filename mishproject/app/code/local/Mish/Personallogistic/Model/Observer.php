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


 public function plorderidUser(Varien_Event_Observer $observer)
 {

  $order = $observer->getEvent()->getOrder()->getId();
  $sessionPLserIdsdsd = Mage::getSingleton('core/session')->getPluserRadioID();

  $userorderModel = Mage::getModel('personallogistic/personallogisticuserorder')->load();
  $userorderModel->setOrderId($order);
  $userorderModel->setPlUserId($sessionPLserIdsdsd);
  $userorderModel->save();

  $plModel = Mage::getModel('personallogistic/personallogistic')->load($sessionPLserIdsdsd);

  $pluseremail = $plModel->getMail();
  $pluserFirstname = $plModel->getFirstname();
  $pluserLastname = $plModel->getLastname();

  $pluserfullName = $pluserFirstname." ".$pluserLastname;
  $this->pLogisticOrdersendEmail($pluseremail,$pluserfullName);
  Mage::getSingleton('core/session')->unsPluserRadioID();
 }



/********** send email starts here ************/

    public function pLogisticOrdersendEmail($pluseremail,$pluserfullName)
    {

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);         
        $mailTemplate = Mage::getModel('core/email_template')->loadDefault('personallogisticorderemail');
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
 
        $vars = array('pluser_email'=>$pluseremail,
                    'pluser_name' =>$pluserfullName,
                   
                      );

              
        $storeId = Mage::app()->getStore()->getId();
        $model   = $mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject);
        $model  -> sendTransactional($mailTemplate,$sender,$pluseremail, $pluserfullName, $vars, $storeId);
         
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
    
    
