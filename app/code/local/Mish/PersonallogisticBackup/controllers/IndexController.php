<?php
class Mish_Personallogistic_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
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

		$pluserid = $this->getRequest()->getPost('pluserID');

		if($pluserid == ''){

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

		         
		$personalModel = Mage::getModel('personallogistic/personallogistic')->load();
		$personalModel->setFirstname($firstname);
		$personalModel->setLastname($lastname);
		$personalModel->setDob($dob);
		$personalModel->setMail($mail);
		$personalModel->setRut($rut);
		$personalModel->setTransport($transport);
		$personalModel->setRegion($implode_region);
		$personalModel->setPrice($price);
		$personalModel->setStatus(2);
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

			  $personalModelupdate->save();



		Mage::getSingleton("core/session")->addSuccess("You are successfully registered."); 
		$this->_redirectReferer();
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

			  $personalModelupdate->save();



		Mage::getSingleton("core/session")->addSuccess("Your profile is updated."); 
		$this->_redirectReferer();

	}

    }
}
