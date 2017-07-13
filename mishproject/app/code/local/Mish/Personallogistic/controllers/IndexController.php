<?php
class Mish_Personallogistic_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }

    public function signupformAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }

    public function updateorderstatusAction()
    {
		$rowId = $this->getRequest()->getParam('id');
		$pluserorderModel = Mage::getModel('personallogistic/personallogisticuserorder')->load($rowId);
		$pluserorderModel->setOrderStatus(1);
		$pluserorderModel->save();
		Mage::getSingleton("core/session")->addSuccess("You changed your order status to delivered"); 
		$this->_redirectReferer();
    }


    public function plcheckoutvalueSetAction()
    {
    	$plusersessionId = $this->getRequest()->getParam('id');
        $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
    	$explodeData = explode(',', $plusersessionId);
    	$sessUserId = $explodeData[0];
    	$cost = $explodeData[1];
    	$realcost = $explodeData[2];

    	$Rcost = explode($currencySymbol,$realcost);

    	 $setPrice = $cost;

    	 echo $setPrice." ".$currencySymbol;

    	$sessionPLserId = Mage::getSingleton('core/session')->setPluserRadioID($sessUserId);
    	$sessioncost = Mage::getSingleton('core/session')->setPluserCost($setPrice);

    	

    	//echo "-->". //$sessionPLserIdsdsd = Mage::getSingleton('core/session')->getPluserradioID();
    
    }

     public function loginAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }

     public function logoutAction()
    {
		 Mage::getSingleton('core/session')->unsPlsessionId();
         $this->_redirect('personallogistic/index/login');
    }

     public function dashboardAction()
    {
    	$plsessionId = Mage::getSingleton('core/session')->getPlsessionId();
    		if($plsessionId == ""){

    			$this->_redirect('personallogistic/index/login');
            }else{
		$this->loadLayout();     
		$this->renderLayout();
	  }
    }

     public function orderdashboardAction()
    {
    	$plsessionId = Mage::getSingleton('core/session')->getPlsessionId();
    		if($plsessionId == ""){

    			$this->_redirect('personallogistic/index/login');
            }else{
		$this->loadLayout();     
		$this->renderLayout();
	  }
    }
     public function plloginAction()
    {

	     $logindata= $this->getRequest()->getParams();
	     $email=$logindata['email'];
	     $password=$logindata['password'];
	    

        $collection = Mage::getModel('personallogistic/personallogistic')->getCollection() ->addFieldToSelect(array('mail', 'password','status'))
                 ->addFieldToFilter('mail', array('eq' => $email)) 
                 ->addFieldToFilter('password', array('eq' => $password))
                 ->addFieldToFilter('status', array('eq' => 1));
                
	     $data= $collection->getData();
	     foreach ($data as $pldata) {
	     $sessionId=$pldata['personallogistic_id'];
             }

                $count = count($collection->getData());
                if ($count==1) {
                Mage::getSingleton('core/session')->setPlsessionId($sessionId);
                $this->_redirect('personallogistic/index/dashboard');
     } 
                else{
           
                    Mage::getSingleton('core/session')->addError('Please check your Email and Password.');
                    Mage::getSingleton('core/session')->addError('If you are a registered logistic user than wait for the admin approval. You will receive confirmation mail after approval.');
                    $this->_redirect('personallogistic/index/login');
                }

    }
    public function personalLogisticDetailsAction()
    {
		$data = $this->getRequest()->getPost();
		$hash = md5( rand(0,1000) );

		// echo "<pre>+++";
		// print_r($data);
		// exit;

		$pluserid = $this->getRequest()->getPost('pluserID');

		if($pluserid == ''){

		$profilepic = $_FILES;
		
		$firstname = $data['firstname'];
		$lastname = $data['lastname'];
		$dob = $data['dob'];
		$rut = $data['rut'];
		$mail = $data['mail'];
		$transport = $data['transport'];
		switch ($transport) {
					case 'Bicycle':
					    $transport_weight='10';
						break;
					case 'Small Car':
					$transport_weight='20';
						break;
					case 'Medium Auto':
						$transport_weight='20';
						break;
					case 'Big Car':
						$transport_weight='20';
						break;
					case 'Heavy Duty Truck':
						$transport_weight='20';
						break;
					case 'Walking':
						$transport_weight='4';
						break;					
					default:
						$transport_weight='0';
						break;
				}		
		$region = $data['region'];
		$implode_region = implode(',', $region);
		$price = $data['price'];

		$password = $data['password'];
		$confirmpassword = $data['confirmpassword'];

	if($password == $confirmpassword){

     
		$personalModel = Mage::getModel('personallogistic/personallogistic')->load();
		$personalModel->setFirstname($firstname);
		$personalModel->setLastname($lastname);
		$personalModel->setTransportweight($transport_weight);
		$personalModel->setDob($dob);
		$personalModel->setMail($mail);
		$personalModel->setRut($rut);
		$personalModel->setPassword($password);
		$personalModel->setTransport($transport);
		$personalModel->setRegion($implode_region);
		$personalModel->setPrice($price);
		$personalModel->setHash($hash);
		$personalModel->setStatus(2);
		$personalModel->setCreatedTime(now());
		//$personalModel->setUpdateTime(2);

		$personalModel->save();

		$id = $personalModel->getPersonallogisticId();

		$personalModelupdate = Mage::getModel('personallogistic/personallogistic')->load($id);

		if(strlen($_FILES['profilepic']['name'])>0){
						$extension = pathinfo($_FILES["profilepic"]["name"], PATHINFO_EXTENSION);
						$temp1 = explode(".",$_FILES["profilepic"]["name"]);
	                    echo "img- ".$img = $temp1[0].rand(1,99999).$loid.'.'.$extension;
						$personalModelupdate->setProfilepic($img);
					}
			
			 $target =Mage::getBaseDir().'/media/personallogistic/profilepic/';
		 $targetimage = $target.$img; 
	
			 move_uploaded_file($_FILES['profilepic']['tmp_name'],$targetimage);

		if(strlen($_FILES['personalid']['name'])>0){
								$extensions = pathinfo($_FILES["personalid"]["name"], PATHINFO_EXTENSION);
								$temp11 = explode(".",$_FILES["personalid"]["name"]);
			                    $img2 = $temp11[0].rand(1,99999).$loid.'.'.$extensions;
								$personalModelupdate->setPersonalId($img2);
							}

				

			 $targets =Mage::getBaseDir().'/media/personallogistic/personalId/';
			 $targetimages = $targets.$img2; 
			 move_uploaded_file($_FILES['personalid']['tmp_name'],$targetimages);

		if(strlen($_FILES['personalid2']['name'])>0){
								$extensions = pathinfo($_FILES["personalid2"]["name"], PATHINFO_EXTENSION);
								$temp11 = explode(".",$_FILES["personalid2"]["name"]);
			                    $img2 = $temp11[0].rand(1,99999).$loid.'.'.$extensions;
								$personalModelupdate->setPersonalId2($img2);
							}

				

			 $targets =Mage::getBaseDir().'/media/personallogistic/personalId/';
			 $targetimages = $targets.$img2; 
			 move_uploaded_file($_FILES['personalid2']['tmp_name'],$targetimages);

			  $personalModelupdate->save();



		Mage::getSingleton("core/session")->addSuccess("Your account has been made, <br /> please verify it by clicking the activation link that has been send to your email."); 
           $this->mailverification($mail,$hash);
		$this->_redirectReferer();
	}else{
		Mage::getSingleton("core/session")->addError("Password do not match"); 
		$this->_redirectReferer();
	}
		

	}else{

		$profilepic = $_FILES;
		
		$firstname = $data['firstname'];
		$lastname = $data['lastname'];
		$dob = $data['dob'];
		$rut = $data['rut'];
		$mail = $data['mail'];
		$transport = $data['transport'];
		$region = $data['region'];
		$implode_region = implode(',', $region);
		$price = $data['price'];

		         
		$personalModel = Mage::getModel('personallogistic/personallogistic')->load($pluserid);
		$personalModel->setFirstname($firstname);
		$personalModel->setLastname($lastname);
		$personalModel->setDob($dob);
		$personalModel->setMail($mail);
		$personalModel->setRut($rut);
		$personalModel->setTransport($transport);
		$personalModel->setTransportweight($transport_weight);
		$personalModel->setRegion($implode_region);
		$personalModel->setPrice($price);
		$personalModel->setStatus(1);
		$personalModel->setCreatedTime(now());
		//$personalModel->setUpdateTime(2);

		$personalModel->save();

		$id = $personalModel->getPersonallogisticId();

		$personalModelupdate = Mage::getModel('personallogistic/personallogistic')->load($id);

		if(strlen($_FILES['profilepic']['name'])>0){
						$extension = pathinfo($_FILES["profilepic"]["name"], PATHINFO_EXTENSION);
						$temp1 = explode(".",$_FILES["profilepic"]["name"]);
	                   $img = $temp1[0].rand(1,99999).$loid.'.'.$extension;
	                    
						$personalModelupdate->setProfilepic($img);
					}
			
			 $target =Mage::getBaseDir().'/media/personallogistic/profilepic/';
			 $targetimage = $target.$img; 
			move_uploaded_file($_FILES['profilepic']['tmp_name'],$targetimage);
			

		if(strlen($_FILES['personalid']['name'])>0){
								$extensions = pathinfo($_FILES["personalid"]["name"], PATHINFO_EXTENSION);
								$temp11 = explode(".",$_FILES["personalid"]["name"]);
			                    $img2 = $temp11[0].rand(1,99999).$loid.'.'.$extensions;
								$personalModelupdate->setPersonalId($img2);
							}

				

			 $targets =Mage::getBaseDir().'/media/personallogistic/personalId/';
			 $targetimages = $targets.$img2; 
			 move_uploaded_file($_FILES['personalid']['tmp_name'],$targetimages);

			 if(strlen($_FILES['personalid2']['name'])>0){
								$extensions = pathinfo($_FILES["personalid2"]["name"], PATHINFO_EXTENSION);
								$temps = explode(".",$_FILES["personalid2"]["name"]);
			                    $imgs = $temps[0].rand(1,99999).$loid.'.'.$extensions;
								$personalModelupdate->setPersonalId2($imgs);
							}

				

			 $targets =Mage::getBaseDir().'/media/personallogistic/personalId/';
			 $targetimages = $targets.$imgs; 
			
			 move_uploaded_file($_FILES['personalid2']['tmp_name'],$targetimages);

			  $personalModelupdate->save();



		Mage::getSingleton("core/session")->addSuccess("Your profile is updated."); 
		$this->_redirectReferer();

	}

    }
     public function mailverification($mail,$hash)
     {
      $to      = $email; // Send email to our user
$subject = "Signup | Verification"; // Give the email a subject 
$message = "
 
Thanks for signing up!
Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.

 
Please click this link to activate your account:
http://www.mish.htmlpluscss.com/verify.php?email='.$mail.'&hash='.$hash.'
 
"; // Our message above including the link
                     
$headers = 'From:owner@example.com' . "\r\n"; // Set from headers
mail($to, $subject, $message, $headers); // Send our email
     }
}
