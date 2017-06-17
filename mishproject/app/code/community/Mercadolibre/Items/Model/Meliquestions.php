<?php

class Mercadolibre_Items_Model_Meliquestions extends Mage_Core_Model_Abstract
{

	private $moduleName = "Items";
	private $fileName = "Meliquestions.php";
	
	//message variable
	private $infoMessage = "";
	private $errorMessage = "";
	private $successMessage = "";
	private $errorMessageLog = "";
      
    public function _construct()
    {
        parent::_construct();
        $this->_init('items/meliquestions');
    }
    
    function getAllQuestionFromAPIhit()
    {

        try{             
            $commonModel = Mage::getModel('items/common');
			if(Mage::helper('items')->getMlAccessToken()){
				$access_token = Mage::helper('items')->getMlAccessToken();
			} else {
				$this->errorMessage = "Error :: Access Token Not Found OR Invalid";
				$commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('items')->__($this->errorMessage));
				$this->_redirect('items/adminhtml_itempublishing/');
				return;	
			}
			/* TRUNCATE TABLE `mercadolibre_questions` before add questions */
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$write->query("TRUNCATE TABLE `mercadolibre_questions`");
            /* Get Base URL Id */
            if(Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore())){
                    $api_url = Mage::getStoreConfig("mlitems/mltokenaccess/mlapiurl",Mage::app()->getStore());
            } else {
                    $this->errorMessage = "Error :: Api Url Not Found OR Invalid";
                    $this->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessage);
                    $commonModel->sendNotificationMail($this->to, 'ML Catergories All Data Cron Error', $this->errorMessage);
            }
            
            $this->to = Mage::getStoreConfig("mlitems/meligeneralsetting/notificationemailid", Mage::app()->getStore());
			$storeId = Mage::helper('items')->_getStore()->getId();
			$item 	=  Mage::getModel('items/mercadolibreitem')
			  		-> getCollection()
			  		-> setOrder('meli_item_id', 'ASC')
			  		-> addFieldToFilter('meli_item_id', array('neq' => ''))
			  		-> addFieldToSelect('meli_item_id')
					-> addFieldToSelect('category_id')
					-> addFieldToFilter('mcm.store_id',$storeId);
			$item 	-> getSelect()
					-> join( array('mcm' => 'mercadolibre_categories_mapping'), 'mcm.mage_category_id  = main_table.category_id ', array("store_id"));

			if(count($item->getData())> 0){
				foreach($item->getData() as $value){    
					if(!empty($value['meli_item_id']))
					{
						/*  Get & save all questions json data into DB table meli_questions */
						$service_url = $api_url.'/questions/search?item_id='.$value['meli_item_id'].'&access_token='.$access_token;               
						$jsonDataResp = $commonModel ->meliConnect($service_url);
						$arryResp = $jsonDataResp['json']['questions'];
						$i=0;
						if(count($arryResp) > 0){
							foreach($arryResp as $data)
							{
							   $meliquestions = Mage::getModel('items/meliquestions');
							   $dataArry = array(                            
												'question_id'=>trim($data['id']),    
												'question'=>$commonModel ->inputData($data['text']),
												'itemid'=>$data['item_id'],
												'question_date'=>$data['date_created'],
												'answer_date'=>$data['date_created'],    
												'created_at'=>$data['date_created'],
												'status' => $data['status']
							  					 ); 
								if(isset($data['answer']['buyer'])){
									$dataArry['buyer'] = $data['answer']['buyer'];
								}
                     		   if(trim($data['answer']['text'])!=''){
									$dataArry['answer'] = $commonModel->inputData($data['answer']['text']); 
							   }	
							   $collectionVar = Mage::getModel('items/meliquestions')->getCollection()->addFieldToFilter('question_id',trim($data['id']));
							   $questionArr = $collectionVar->getData();
							   if(isset($questionArr['0']['id']) && count($questionArr) > 0){
								  $dataArry['id'] = $questionArr['0']['id'];                           
							   } 
							   $meliquestions->setData($dataArry);                        
							   $meliquestions->save();                                                                                            
							} 
						}                   
					}            
				}
			} else {
				 $this->errorMessageLog = "There is no item_id found to get question".
				 $commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $this->errorMessageLog);
			}
        }
        catch(Exception $e){
            $commonModel->saveLogger($this->moduleName, "Exception", $this->fileName, $e->getMessage());
            $commonModel->sendNotificationMail($this->to, 'Exception::ML Catergories All Data Cron', $e->getMessage());
	}
    }
}