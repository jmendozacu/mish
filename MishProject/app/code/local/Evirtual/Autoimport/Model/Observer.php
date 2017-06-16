<?php
class Evirtual_Autoimport_Model_Observer
{
    protected $entryurl;
	protected $title;
	protected $summary;
	protected $categoryskipp;
	protected $categoryimport;
	protected $productskipp;
	protected $productimport;
	
	public function AutoImportSetup()
    {
		
		$enable = Mage::getStoreConfig('autoimport/tools/enable');
		
		if($enable){
				
				set_time_limit(0);
				ini_set('max_input_time', '600000000000000');
				ini_set('memory_limit', '2048M');
				$entries	= Mage::getModel('autoimport/entry')->getCollection()->addFieldToFilter('status', 1);
				foreach($entries as $entry){
					$this->runEntry($entry);
				}
		}
		
	}
	
	public function runEntry($entry){
		
			$type=$entry->getType();
			$name=$entry->getTitle();
			$attributemapping=unserialize($entry->getAttributemapping());
			$this->entryurl=$entry->getUrl();
			$catalogtype=$entry->getCatalogtype();
			$Attdb = $attributemapping[0]['db'];
			$Attfile = $attributemapping[0]['file'];
			
			$activiy=Mage::getModel('autoimport/activiy');
			
			if($type=="xml"){
				
				if($catalogtype=="product"){					
					
					$this->productskipp=0;
					$this->productimport=0;
					
					$this->title=$name;	
					$this->XMLProductMappingArray($Attdb,$Attfile);
					
					$activiy->setSummary($this->summary);
					$activiy->setTitle($this->title);
					$activiy->save();
					
					
				}else{
					
				}
			}else if($type=="xlsx"){
				
				$this->productskipp=0;
				$this->productimport=0;
					
				$this->title=$name;	
				$this->XLSXProductMappingArray($Attdb,$Attfile);
				
				$activiy->setSummary($this->summary);
				$activiy->setTitle($this->title);
				$activiy->save();
				
			}else{
				
				if($catalogtype=="product"){					
					
					//$this->XMLMappingArray($Attdb,$Attfile);
				
				}elseif($catalogtype=="category"){
					
					$this->categoryskipp=0;
					$this->categoryimport=0;
					
					$this->title=$name;	
					$this->CSVProductMappingArray($Attdb,$Attfile);
					
					$activiy->setSummary($this->summary);
					$activiy->setTitle($this->title);
					$activiy->save();
					
				}elseif($catalogtype=="stockupdate"){
					
					$this->categoryskipp=0;
					$this->categoryimport=0;
					
					$this->title=$name;	
					$this->CSVProductStockMappingArray($Attdb,$Attfile);
					
					$activiy->setSummary($this->summary);
					$activiy->setTitle($this->title);
					$activiy->save();
					
				}
				
			}
		
	}
	
	protected function CSVProductStockMappingArray($ProductAttDb,$ProductAttFile){
		
		
		array_shift($ProductAttDb);
		
		array_shift($ProductAttFile);
		
		
		
		$file_handle = fopen($this->entryurl,'r');
			$i=0;
			$arrayAttTtitle=array();
			while (!feof($file_handle) ) {
				$line_of_text = fgetcsv($file_handle, 1024);
					
					if($i==0){
						for($j=0;$j<count($line_of_text);$j++){
							
							//array_push($arrayAttTtitle,$line_of_text[$j]);
			
							if (in_array($line_of_text[$j],$ProductAttFile)) {
								
								$keyNew = array_search($line_of_text[$j], $ProductAttFile);
								$DbAtt=$ProductAttDb[$keyNew];
								
								array_push($arrayAttTtitle,$DbAtt);
								
								
							
							}
							
						}	
					}
					
					if($i>0){
						if(is_array($line_of_text)){
					
								$StackData=array();
								$StackSubArray=array();
								$sku='';
								for($j=0;$j<count($line_of_text);$j++){
									
										if($arrayAttTtitle[$j]=="sku"){
											$sku=	$line_of_text[$j];	
										}else{
										
										if($line_of_text[$j]=="In Stock")	
										{
											array_push($StackData,array($arrayAttTtitle[$j]=>1));
										}elseif($line_of_text[$j]=="Out Stock"){
											array_push($StackData,array($arrayAttTtitle[$j]=>0));
										}else{
											array_push($StackData,array($arrayAttTtitle[$j]=>$line_of_text[$j]));
										}
										}
								}
								//Zend_Debug::dump($arrayAttTtitle);
								//array_push($StackData,$StackSubArray);
								
								//Zend_Debug::dump($CategoryData);
								$this->_CSVStockUpload($StackData,$sku);	
							
						}
						
						
					}
				$i++;
			}
			
			$import='Total of '.$this->categoryimport.' Stock(s) were successfully Imported <br/>';
			
			$this->summary=$import.$skiped;
		
	}
	
	
	protected function _CSVStockUpload($StackArray,$sku){
	
		
		//$sku=10057;
		$product = Mage::helper('catalog/product')->getProduct($sku, 0, 'sku');
		
		//Zend_Debug::dump($product->getData());
		
		if($product->getId()){
					
					$stockItem = Mage::getModel('cataloginventory/stock_item');
					$stockItem->loadByProduct($product);
					
						foreach($StackArray as $Stack){
							
							foreach($Stack as $key => $value){
								
								if($key!="price"){							
									$stockItem->setData($key,$value);
								}else{
									$product->setPrice($value);	
								}
							}	
						}
					$this->categoryimport=$this->categoryimport+1;
					$stockItem->save();
					$product->save();
		}
		
		
	}
	
	protected function CSVProductMappingArray($ProductAttDb,$ProductAttFile){
		
		array_shift($ProductAttDb);
		
		array_shift($ProductAttFile);
		
		
		
		$file_handle = fopen($this->entryurl,'r');
			$i=0;
			$arrayAttTtitle=array();
			while (!feof($file_handle) ) {
				$line_of_text = fgetcsv($file_handle, 1024);
					
					if($i==0){
						for($j=0;$j<count($line_of_text);$j++){
							
							//array_push($arrayAttTtitle,$line_of_text[$j]);
			
							if (in_array($line_of_text[$j],$ProductAttFile)) {
								
								$keyNew = array_search($line_of_text[$j], $ProductAttFile);
								$DbAtt=$ProductAttDb[$keyNew];
								
								array_push($arrayAttTtitle,$DbAtt);
								
								
							
							}
							
						}	
					}
					
					if($i>0){
						if(is_array($line_of_text)){
					
								$CategoryData=array();
								$CategorySubArray=array();
								
								for($j=0;$j<count($line_of_text);$j++){
									
										array_push($CategoryData,array($arrayAttTtitle[$j]=>$line_of_text[$j]));
																		
								}
								//Zend_Debug::dump($arrayAttTtitle);
								array_push($CategoryData,$CategorySubArray);
								
								//Zend_Debug::dump($CategoryData);
								$this->_CSVCategoryUpload($CategoryData);	
							
						}
						
						
					}
				$i++;
			}
			
			$import='Total of '.$this->categoryimport.' Category(s) were successfully Imported <br/>';
			$skiped='Total of '.$this->categoryskipp.' Category(s) were successfully Updated <br/>';
			$this->summary=$import.$skiped;
			
			
	}
	
	protected function _CSVCategoryUpload($categoryArray){
		
		$storeId=1;
		
		$rootId=Mage::app()->getStore($storeId)->getRootCategoryId();
		$rootcate=Mage::getModel('catalog/category')->load($rootId);
		
		$categoryData = array();
		$level=1;
		$parentId=$rootcate->getEntityId();
		$path=$rootcate->getPath();
		$id=0;
		
		foreach($categoryArray as $category){
			
			foreach($category as $key => $value){
					if($key=="id"){
						$path.="/".$value;
						$id=$value;
					}
					
					if($key!=1){
						$categoryData[$key]=$value;
					}
			}
				
			
		}
		
		$categoryCheck = Mage::getModel('catalog/category')->load($id);
	
			if(null===$categoryCheck->getId()){
				
				$category = Mage::getModel('catalog/category');
		
				
				$categoryData['display_mode']='PRODUCTS';
				$categoryData['is_active']=1;
				$categoryData['is_anchor']=1;
				$categoryData['attribute_set_id']=$category->getDefaultAttributeSetId();
				$categoryData['level']=$level;
				$categoryData['custom_design_apply']=1;
				$category->setData($categoryData);
				$category->setId($id);
				$category->setPath($path);
				$category->setParentId($parentId);
				$category->save();
				
	
				$this->categoryimport=$this->categoryimport+1;
			
			}else{
				
				$category = Mage::getModel('catalog/category')->load($id);
				$category->setData($categoryData);
				$category->save();
				
				$this->categoryskipp=$this->categoryskipp+1;	
			}
		
	}
	
	protected function XLSXProductMappingArray($ProductAttDb,$ProductAttFile){
		
		$xlsxArray=Mage::helper('autoimport')->ArrayData($this->entryurl);
		
		/*Zend_Debug::dump($xlsxArray);
		exit;*/
		array_shift($ProductAttDb);
		
		array_shift($ProductAttFile);
		$arrayAttTtitle=array();
		for($i=0;$i<count($xlsxArray);$i++){
			
			if($i==0){
				
				for($j=0;$j<count($xlsxArray[$i]);$j++){
					
					if (in_array($xlsxArray[$i][$j],$ProductAttFile)) {
						
						//Zend_Debug::dump($xlsxArray[$i][$j]);
						
						//if($xlsxArray[$i][$j]!=""){			
							$keyNew = array_search($xlsxArray[$i][$j], $ProductAttFile);
							$DbAtt=$ProductAttDb[$keyNew];
							
							/*Zend_Debug::dump($DbAtt);
							exit;*/
							$arrayAttTtitle[$j]=$DbAtt;
							//array_push($arrayAttTtitle,$DbAtt);
						//}
						
					
					}
					//Zend_Debug::dump($xlsxArray[$i][$j]);
					
				}
				//Zend_Debug::dump($arrayAttTtitle);
				
					
			}
			//$_category = Mage::getModel('catalog/category')->loadByAttribute('name', 'Acoustic Guitars');
			if($i>0){
				if(is_array($xlsxArray[$i])){
			
						$ProductData=array();
						$ProductSubArray=array();
						
						//Zend_Debug::dump($xlsxArray[$i]);
						
						for($j=0;$j<count($xlsxArray[$i]);$j++){
							
							if($j==0){
								
								if($xlsxArray[$i][$j] !=""){
									 if($arrayAttTtitle[$j]!=""){	
										array_push($ProductData,array($arrayAttTtitle[$j]=>$xlsxArray[$i][$j]));
									 }
								}
								
							}else if($j==20){
								
								 if($xlsxArray[$i][$j] !="No"){
									 
									 if($arrayAttTtitle[$j]!=""){	
										array_push($ProductData,array($arrayAttTtitle[$j]=>$xlsxArray[$i][$j]));
									 }
									 
								 }
									
							}else if($j==21){
									
								if($xlsxArray[$i][$j] !="E"){
									
									if($xlsxArray[$i][$j] !="P"){
										if($arrayAttTtitle[$j]!=""){	
										array_push($ProductData,array($arrayAttTtitle[$j]=>$xlsxArray[$i][$j]));
										}
									}
									
								}
									
									
							}else{
								if($arrayAttTtitle[$j]!=""){	
										array_push($ProductData,array($arrayAttTtitle[$j]=>$xlsxArray[$i][$j]));
									}
							}
						}
						//Zend_Debug::dump($arrayAttTtitle);
						array_push($ProductData,$ProductSubArray);
						//$ProductData=array_filter($ProductData);
						
							$this->_XLSXProductUpload($ProductData);	
						
					//	exit;
						
					
				}
				
				
			}
			
			
		}
		$import='Total of '.$this->productimport.' Product(s) were successfully Imported <br/>';
		$skiped='Total of '.$this->productskipp.' Product(s) were successfully Updated <br/>';
		$this->summary=$import.$skiped;
		//exit;
	}
	
	
	protected function _XLSXProductUpload($productArray){
		
			$api = new Mage_Catalog_Model_Product_Api();
     
			$attribute_api = new Mage_Catalog_Model_Product_Attribute_Set_Api();
			$attribute_sets = $attribute_api->items();
    		$imported=0;
			$skiped=0;
			$jkjk=0;
			
			$productData = array();
			$productData['website_ids'] = array(1);
			$productData['status'] = 1;
			$productData['productfrom'] = 'xlsx';	
			$stockArray=array('use_config_manage_stock','qty','min_qty','use_config_min_qty','min_sale_qty','use_config_max_sale_qty','max_sale_qty','use_config_max_sale_qty','is_qty_decimal','backorders',
			'notify_stock_qty','is_in_stock','tax_class_id');
			$stockFieldsArray=array('use_config_manage_stock'=>1,'qty'=>1,'min_qty'=>0,'use_config_min_qty'=>1,'min_sale_qty'=>0,'use_config_max_sale_qty'=>1,'max_sale_qty'=>0,'use_config_max_sale_qty'=>0,'is_qty_decimal'=>0,'backorders'=>0,
			'notify_stock_qty'=>1,'is_in_stock'=>1,'tax_class_id'=>0);
			$imageArray=array('image','images','gallery');
			$imageFieldsArray=array();
			$sku=date('Ymdhis');
				
				
				foreach($productArray as $attributes){
					
					
					
					foreach($attributes as $key => $value)
						if(is_int($key)!=true){
							
							
									if(in_array($key,$stockArray)){	
										unset($stockFieldsArray[$key]);
										$stockFieldsArray[$key]=$value;
									}else if(in_array($key,$imageArray)){
											$imagesArray=array();
											$value=json_decode(json_encode((array) $value), 1);;
											
													for($i=0;$i<count($value['images']);$i++){	
													//Get the file
													if(count($value['images'])==1){	
														$image=$value['images'];
													}else{
														$image=$value['images'][$i];
													}
													
													//echo $image;
													
													$url_arr = explode ('/', $image);
													$ct = count($url_arr);
													$name = $url_arr[$ct-1];
													$name_div = explode('.', $name);
													$ct_dot = count($name_div);
													$img_type = $name_div[$ct_dot -1];
													
													$staticUrl='http://www.giocorama.it/static/img/'.$name;
													
													//$content = file_get_contents($image);
													$content = file_get_contents($staticUrl);
													
													//Store in the filesystem.
													$path = Mage::getBaseDir('var') . DS .'import\evirtual\media\http' ;
													$datetime=Mage::getModel('core/date')->timestamp(time());
													$xmlFile = $path."\\".$name;
													$xmlHandle = fopen($xmlFile, "w");
													fwrite($xmlHandle, $content);
													fclose($xmlHandle);
													
													array_push($imagesArray,$xmlFile);
													
												}
													
											$imageFieldsArray[$key]=$imagesArray;
										
									}else{
										if($key=="sku"){	
											$productData[$key]=str_replace(" ","",$value);
										}else{
											$productData[$key]=$value;
										}
									}
						}
				}
				
				
				
						/*Zend_Debug::dump($productData['category_ids']);
						exit;*/
						
						//$parentIds=$productData['category_ids']['parent_id'];	
						//Zend_Debug::dump($productData);
						
						
						$categoryIds=$this->createXLSXCategoryes($productData['category_ids'],$productData['subcategory_ids']);
							
							/*Zend_Debug::dump($categoryIds);
							
							exit;*/
											
						$productData['category_ids']=$categoryIds;
						
					
				
					$productSearch = Mage::getModel('catalog/product');
 
					$productSearchId = $productSearch->getIdBySku($sku);
					if(!$productSearchId){
				
							$new_product_id = $api->create('simple',$attribute_sets[0]['set_id'],$sku,$productData);
							$stockItem = Mage::getModel('cataloginventory/stock_item');
							$stockItem->loadByProduct( $new_product_id );
							
							foreach($stockFieldsArray as $key => $value){
								$stockItem->setData($key,$value);
							}
							$stockItem->save();
							$product = Mage::getModel('catalog/product')->load($new_product_id);
							foreach($imageFieldsArray as $key => $value){
								$product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
								/*if($imagesfrom=="local"){	*/
									
									for($im=0;$im<count($value);$im++){	
										/*echo $value[$im];
										exit;*/
										/*$product->addImageToMediaGallery (Mage::getBaseDir('var') . DS .'import/evirtual/media/http/'.$value[$im], array ('image','small_image','thumbnail'), false, false);*/
										$product->addImageToMediaGallery ($value[$im], array ('image','small_image','thumbnail'), false, false);
										
									}
										
								/*}else{
									$product->addImageToMediaGallery ($value, array ('image','small_image','thumbnail'), false, false);
								}*/
							}
							Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
							
			 				$product->setCategoryIds($categoryIds);
							$product->save();
							$this->productimport=$this->productimport+1;
					}else{
						
							$product = Mage::getModel('catalog/product')->load($productSearchId);
							foreach($productData as $key=>$value){
								$product->setData($key,$value);	
							}
							
							//Zend_Debug::dump($product->getData());
							
							$stockItem = Mage::getModel('cataloginventory/stock_item');
							$stockItem->loadByProduct($productSearchId);
							foreach($stockFieldsArray as $key => $value){
								$stockItem->setData($key,$value);
							}
							$stockItem->save();
							$product = Mage::getModel('catalog/product')->load($productSearchId);
							/*foreach($imageFieldsArray as $key => $value){
								
								
								
								
								$product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
								
									
									for($im=0;$im<count($value);$im++){	
									
										$product->addImageToMediaGallery ($value[$im], array ('image','small_image','thumbnail'), false, false);
										
									}
								
							}*/
							$product->setCategoryIds($categoryIds);
							$product->save();
							
						$this->productskipp=$this->productskipp+1;
					}
					
		
		
			
		
	}
	
	protected function createXLSXCategoryes($parentCategoryIds,$childCategoryIds){
		
		$storeId=1;
		$assignCategory=array();
		$rootId=Mage::app()->getStore($storeId)->getRootCategoryId() ;
		$rootcate=Mage::getModel('catalog/category')->load($rootId);
		
		//Zend_Debug::dump($rootId);
		
		// Parent
		
		$name=$parentCategoryIds;
		$parentforchild='';
		$parentpathforchild='';
		$categoryCheck = Mage::getModel('catalog/category')->loadByAttribute('name',$name);
		
		//Zend_Debug::dump($categoryCheck->getData());
		//exit;
		if(!$categoryCheck){
			
			$parentId=$rootId;
			
			$category = Mage::getModel('catalog/category');
			$category->setName($name)
			->setIsActive(1)                       //activate your category
			->setDisplayMode('PRODUCTS')
			->setIsAnchor(1)
			->setCustomDesignApply(1)
			->setAttributeSetId($category->getDefaultAttributeSetId())
			->setParentId($rootId);
			$category->save();
			
			
			$parentforchild=$category->getId();
			
			$path=$rootcate->getPath();
			$path.="/".$parentforchild;
			
			$category->setPath($path);
			$category->setParentId($rootId);
			$category->save();
			$parentpathforchild=$path;
			array_push($assignCategory,$parentforchild);
				
		}else{
			$parentforchild=$categoryCheck->getId();
			$parentpathforchild=$categoryCheck->getPath();
			array_push($assignCategory,$parentforchild);
		}
		
		//Zend_Debug::dump($childCategoryIds);
		
		// Child
		
		$name=$childCategoryIds;
		
		$categoryCheck = Mage::getModel('catalog/category')->loadByAttribute('name',$name);
		
		if(!$categoryCheck){

			$parentforchild;
			$category = Mage::getModel('catalog/category');
			$category->setName($name)
			->setIsActive(1)                       //activate your category
			->setDisplayMode('PRODUCTS')
			->setIsAnchor(1)
			->setCustomDesignApply(1)
			->setAttributeSetId($category->getDefaultAttributeSetId())
			->setParentId($parentforchild);
			$category->save();
			
			
			$parentforchild=$category->getId();
			
			$path=$parentpathforchild;
			$path.="/".$parentforchild;
			
			$category->setPath($path);
			$category->setParentId($parentforchild);
			$category->save();
			
			array_push($assignCategory,$parentforchild);
				
		}else{
			$parentforchild=$categoryCheck->getId();
			array_push($assignCategory,$parentforchild);
		}
		
		return $assignCategory;
	}
	
	
	protected function XMLProductMappingArray($ProductAttDb,$ProductAttFile){
		
		$flashRAW = simplexml_load_file($this->entryurl,null, LIBXML_NOCDATA);
		
		
		
		array_shift($ProductAttDb);
		
		array_shift($ProductAttFile);
		
		foreach($flashRAW->children() as $parts){
						
							$parts=json_decode(json_encode((array) $parts), 1);
							
							if(is_array($parts)){
								
									$productArray=array();
									$productSubArray=array();
									$productData=array();
																				
												if(count($parts)>0){
													
													foreach($parts as $key => $value){	
															
															if (in_array($key,$ProductAttFile)) {
																	
																	$keyNew = array_search($key, $ProductAttFile);
																	$DbAtt=$ProductAttDb[$keyNew];
																	array_push($productSubArray,array($DbAtt=>$value));
															}
													}
												
												}else{
													
													foreach($sections as $key => $value){
															
															if (in_array($key,$ProductAttFile)) {
																	$keyNew = array_search($key, $ProductAttFile);
																	$DbAtt=$ProductAttDb[$keyNew];
																	array_push($productSubArray,array($DbAtt=>$value));
															}
																
													}
												}
											
											array_push($productArray,$productSubArray);	
												
											$this->_XMLProductUpload($productArray);
							
							}
				
			}
			
			
			$import='Total of '.$this->productimport.' Product(s) were successfully Imported <br/>';
			$skiped='Total of '.$this->productskipp.' Product(s) were successfully Updated <br/>';
			$this->summary=$import.$skiped;
		
	}
	
	protected function _XMLProductUpload($productArray){
		
		$api = new Mage_Catalog_Model_Product_Api();
     
			$attribute_api = new Mage_Catalog_Model_Product_Attribute_Set_Api();
			$attribute_sets = $attribute_api->items();
    		$imported=0;
			$skiped=0;
			$jkjk=0;
			foreach($productArray as $products){
				
				$productData = array();
				$productData['website_ids'] = array(1);
				$productData['status'] = 1;	
				$stockArray=array('use_config_manage_stock','qty','min_qty','use_config_min_qty','min_sale_qty','use_config_max_sale_qty','max_sale_qty','use_config_max_sale_qty','is_qty_decimal','backorders',
				'notify_stock_qty','is_in_stock','tax_class_id');
				$stockFieldsArray=array('use_config_manage_stock'=>1,'qty'=>1,'min_qty'=>0,'use_config_min_qty'=>1,'min_sale_qty'=>0,'use_config_max_sale_qty'=>1,'max_sale_qty'=>0,'use_config_max_sale_qty'=>0,'is_qty_decimal'=>0,'backorders'=>0,
				'notify_stock_qty'=>1,'is_in_stock'=>1,'tax_class_id'=>0);
				$imageArray=array('image','images','gallery');
				$imageFieldsArray=array();
				$sku=date('Ymdhis');
				
				
				foreach($products as $attributes){
					
					foreach($attributes as $key => $value)
						if(is_int($key)!=true){
							
								
								
									if(in_array($key,$stockArray)){	
										unset($stockFieldsArray[$key]);
										$stockFieldsArray[$key]=$value;
									}elseif(in_array($key,$imageArray)){
									
										
											$imagesArray=array();
											$value=json_decode(json_encode((array) $value), 1);;
											$imagval=array_keys($value);
											
									//		Zend_Debug::dump($imagval[0]);
											
											if(is_int($imagval[0])){
												$image=$value[0];
													$url_arr = explode ('/', $image);
													$ct = count($url_arr);
													$name = $url_arr[$ct-1];
													$name_div = explode('.', $name);
													$ct_dot = count($name_div);
													$img_type = $name_div[$ct_dot -1];
													
													//echo $name."_________".$img_type."<br/>";
													//exit;
													
													$content = file_get_contents($image);
													//print_r($content);
													//exit;
													//Store in the filesystem.
													$path = Mage::getBaseDir('var') . DS .'import/evirtual/media/http' ;
													$datetime=Mage::getModel('core/date')->timestamp(time());
													$xmlFile = $path."/".$name;
													$xmlHandle = fopen($xmlFile, "w");
													fwrite($xmlHandle, $content);
													fclose($xmlHandle);
													
													array_push($imagesArray,$xmlFile);
											}else{
												foreach($value as $key=>$newvalue){
														
															for($i=0;$i<count($newvalue);$i++){	
																if(count($newvalue)==1 && !is_array($newvalue)){	
																	$image=$newvalue;
																}else{
																	$image=$newvalue[$i];
																}
															
															//Zend_Debug::dump("+++++++++++++++++++");
															//Zend_Debug::dump($image);
															//Zend_Debug::dump("+++++++++++++++++++");
															$url_arr = explode ('/', $image);
															$ct = count($url_arr);
															$name = $url_arr[$ct-1];
															$name_div = explode('.', $name);
															$ct_dot = count($name_div);
															$img_type = $name_div[$ct_dot -1];
															
															//Zend_Debug::dump("+++++++++++++++++++");
															//Zend_Debug::dump($name);
															//Zend_Debug::dump("+++++++++++++++++++");
															
															$content = file_get_contents($image);
															//Store in the filesystem.
															$path = Mage::getBaseDir('var').DS.'import/evirtual/media/http' ;
															$datetime=Mage::getModel('core/date')->timestamp(time());
															$xmlFile = $path."/".$name;
															//Zend_Debug::dump();
															$xmlHandle = fopen($xmlFile, "w");
															fwrite($xmlHandle, $content);
															fclose($xmlHandle);
															
															array_push($imagesArray,$xmlFile);
															
														}
													}
											
											}
											
											// Multi node 
													/*foreach($value as $key=>$newvalue){
														
														//Zend_Debug::dump($newvalue);
															for($i=0;$i<count($newvalue);$i++){	
																if(count($newvalue)==1){	
																	$image=$newvalue[$i];
																}else{
																	$image=$newvalue[$i];
																}
															//Zend_Debug::dump($image);
															$url_arr = explode ('/', $image);
															$ct = count($url_arr);
															$name = $url_arr[$ct-1];
															$name_div = explode('.', $name);
															$ct_dot = count($name_div);
															$img_type = $name_div[$ct_dot -1];
															
															//echo $name."_________".$img_type."<br/>";
															//exit;
															
															$content = file_get_contents($image);
															//Store in the filesystem.
															$path = Mage::getBaseDir('var') . DS .'import\evirtual\media\http' ;
															$datetime=Mage::getModel('core/date')->timestamp(time());
															$xmlFile = $path."\\".$name;
															//Zend_Debug::dump();
															$xmlHandle = fopen($xmlFile, "w");
															fwrite($xmlHandle, $content);
															fclose($xmlHandle);
															
															array_push($imagesArray,$xmlFile);
															
														}
													}*/
											
											
													
													
												
													
											$imageFieldsArray[$key]=$imagesArray;
										
									
									}elseif($key=="sku"){
										$sku=$value;	
									}else{
										$productData[$key]=$value;
									}
						}
				}
				
						/*Zend_Debug::dump($productData['category_ids']);
						exit;*/
						
						//$parentIds=$productData['category_ids']['parent_id'];	
						/*Zend_Debug::dump($productData);
						
							exit;*/
						//$categoryIds=$this->createXMLCategoryes($productData['category_ids']);
						$categoryIds=$productData['category_ids'];
											
						//$productData['category_ids']=$categoryIds;
						
						
					
				
					$productSearch = Mage::getModel('catalog/product');
 
					$productSearchId = $productSearch->getIdBySku($sku);
					if(!$productSearchId){
				
							$new_product_id = $api->create('simple',$attribute_sets[0]['set_id'],$sku,$productData);
							$stockItem = Mage::getModel('cataloginventory/stock_item');
							$stockItem->loadByProduct( $new_product_id );
							
							foreach($stockFieldsArray as $key => $value){
								$stockItem->setData($key,$value);
							}
							$stockItem->save();
							$product = Mage::getModel('catalog/product')->load($new_product_id);
							foreach($imageFieldsArray as $key => $value){
								$product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
								/*if($imagesfrom=="local"){	*/
									
									for($im=0;$im<count($value);$im++){	
										/*echo $value[$im];
										exit;*/
										/*$product->addImageToMediaGallery (Mage::getBaseDir('var') . DS .'import/evirtual/media/http/'.$value[$im], array ('image','small_image','thumbnail'), false, false);*/
										$product->addImageToMediaGallery ($value[$im], array ('image','small_image','thumbnail'), false, false);
										
									}
										
								/*}else{
									$product->addImageToMediaGallery ($value, array ('image','small_image','thumbnail'), false, false);
								}*/
							}
							Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
							
			 				if($categoryIds != ""){
			 				$product->setCategoryIds($categoryIds);
							}
							$product->save();
							$this->productimport=$this->productimport+1;
					}else{
						
							$product = Mage::getModel('catalog/product')->load($productSearchId);
							foreach($productData as $key=>$value){
								$product->setData($key,$value);	
							}
							
							//Zend_Debug::dump($product->getData());
							
							$stockItem = Mage::getModel('cataloginventory/stock_item');
							$stockItem->loadByProduct($productSearchId);
							foreach($stockFieldsArray as $key => $value){
								$stockItem->setData($key,$value);
							}
							$stockItem->save();
							//$product = Mage::getModel('catalog/product')->load($productSearchId);
							/*foreach($imageFieldsArray as $key => $value){
								
								
								
								
								$product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
								
									
									for($im=0;$im<count($value);$im++){	
									
										$product->addImageToMediaGallery ($value[$im], array ('image','small_image','thumbnail'), false, false);
										
									}
								
							}*/
							if($categoryIds != ""){
							$product->setCategoryIds($categoryIds);
							}
							$product->save();
							
						$this->productskipp=$this->productskipp+1;
					}
					
					
					/*Mage::Log('Total of %d record(s) were successfully Imported', count($imported));
					Mage::Log('Total of %d record(s) were successfully Updated', count($skiped));*/
			
			}
		
			
		
	}
	
	protected function createXMLCategoryes($categoryIds){
		
		$categoryIds=json_decode(json_encode((array) $categoryIds), 1);
		
		$assignCategory=array();
		
		$parentIds=$categoryIds['parent_id'];
		$parentNames=$categoryIds['parent_name'];	
		
		$ChildIds=$categoryIds['category_id'];
		$ChildNames=$categoryIds['category_name'];	
		
		$storeId=1;
		
		$rootId=Mage::app()->getStore($storeId)->getRootCategoryId() ;
		$rootcate=Mage::getModel('catalog/category')->load($rootId);
		
		
		
		////////// Parent ///////////////////////
		
		for($i=0;$i<count($parentIds);$i++){
			
			$id=$parentIds[$i];
			$name=$parentNames[$i];
			
			if(!is_array($id)){
				
			
					array_push($assignCategory,$id);
					
					$categoryCheck = Mage::getModel('catalog/category')->load($id);
			
					if(null===$categoryCheck->getId()){
						
							if($rootcate->getPath()!=""){
									$path=$rootcate->getPath();
									$path.="/".$id;
									$level=1;
									$parentId=$rootId;
									$category = Mage::getModel('catalog/category');
									$category->setName($name)
									->setIsActive(1)                       //activate your category
									->setDisplayMode('PRODUCTS')
									->setIsAnchor(1)
									->setCustomDesignApply(1)
									->setAttributeSetId($category->getDefaultAttributeSetId())
									->setLevel($level)
									->setParentId($parentId);
									$category->setId($id);
									$category->setPath($path);
									$category->save();
							}
						
					}
			}
			
		}
		
		//////////// end parent ///////////////////
		
		
		////// CHILD ////////////////////
		
		
		for($i=0;$i<count($ChildIds);$i++){
			
			$Parentid=$parentIds[$i];
			$Parentname=$parentNames[$i];
			
			$id=$ChildIds[$i];
			$name=$ChildNames[$i];
			
			if(!is_array($id)){
				
						array_push($assignCategory,$id);
						
						$categoryCheck = Mage::getModel('catalog/category')->load($id);
						$categoryParent = Mage::getModel('catalog/category')->load($Parentid);
						
						
						if(null===$categoryCheck->getId()){
							
								if($categoryParent->getPath()!=""){
										$path=$categoryParent->getPath();
										$path.="/".$id;
										$level=2;
										$parentId=$categoryParent->getEntityId();
										$category = Mage::getModel('catalog/category');
										$category->setName($name)
										->setIsActive(1)                       //activate your category
										->setDisplayMode('PRODUCTS')
										->setIsAnchor(1)
										->setCustomDesignApply(1)
										->setAttributeSetId($category->getDefaultAttributeSetId())
										->setLevel($level)
										->setParentId($parentId);
										$category->setId($id);
										$category->setPath($path);
										$category->save();
								}
							
						}
				}
		}
		
		//////// CHild END //////////
		
		return $assignCategory;
		
		
		
		
	}
	
}
?>