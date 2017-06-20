<?php

class Cartin24_Cmsimport_Model_Cmsimport extends Mage_Core_Model_Abstract
{
   private $delimiter = ',';
	
	
	public function importBlock($fileName,$behaviour)
	{
		$file = Mage::getBaseDir('var') . DS .'cmsimport'. DS.$fileName;
		$result = 0; 
		if (($handle = fopen($file, "r")) !== FALSE) {
			$heading = array('Title','Identifier','Content','Is_Active','Stores');
			$row = 0;
			while (($data = fgetcsv($handle, 1000, $this->delimiter)) !== FALSE) {
				$row++;
				$cmsBlockData = array();
				$storesArray =  array();
				if ($row == 1) {
					$headerColumns = $data;
					continue;
				}
				foreach ($heading as $val) {
					if(! in_array(strtolower($val),array_map('strtolower', $headerColumns)))
						Mage::throwException("Incorrect CSV format");
				}
				$staticblock = Mage::getModel('cms/block');
				$id = $data[$this->getColumnIndex($headerColumns,'Identifier')];
				$staticblock->load($id);
				foreach ($headerColumns as $index => $key) {
					$key = strtolower($key);
					switch ($key) {

						case "stores":
							$stores = $data[$index];
							$storesArray = explode(';', $stores);
							if($behaviour){
								$staticblock->setData($key, $storesArray);
								$staticblock->setData('store_id', $storesArray);
							}
							break;	
						default:
							if($behaviour)
								$staticblock->setData($key, html_entity_decode($data[$index]));
							break;

					}
				}
				try {
					if($behaviour)
						$staticblock->save();
					else{
						$cmsBlockData = array(
								'title' => $data[$this->getColumnIndex($headerColumns,'title')],
								'identifier' => $data[$this->getColumnIndex($headerColumns,'identifier')],	
								'content' => $data[$this->getColumnIndex($headerColumns,'content')],
								'is_active' => $data[$this->getColumnIndex($headerColumns,'is_active')],
								'creation_time'=> $data[$this->getColumnIndex($headerColumns,'creation_time')],
								'update_time'=> $data[$this->getColumnIndex($headerColumns,'update_time')],	
								'stores' => $storesArray
								);
						$staticblock->setData($cmsBlockData)->save();
					}	
					$result++; 
						
				} catch (Exception $e) {
						//Mage::throwException($e->getMessage() . ' URL Key = ' . $id);
						//continue;
				}

			
			}
			
		} else {
			throw new Exception('Error opening file ' . $filepath);
		}
		$res = $result.'~'.(($row-1)-$result);
		return $res;
		
	}

public function importPage($fileName,$behaviour)
	{
		
		$file = Mage::getBaseDir('var') . DS .'cmsimport'. DS.$fileName;
		$result = 0; 
		if (($handle = fopen($file, "r")) !== FALSE) {
			$heading = array('Title','Identifier','Content','Is_Active','Stores','Root_Template');
			$row = 0;
			while (($data = fgetcsv($handle, 1000, $this->delimiter)) !== FALSE) {
				$row++;
				$cmsPageData = array();
				$storesArray =  array();
				if ($row == 1) {
					$headerColumns = $data;
					continue;
				}
				foreach ($heading as $val) {
					if(! in_array(strtolower($val),array_map('strtolower', $headerColumns)))
						Mage::throwException("Incorrect CSV format");
				}
				$model = Mage::getModel('cms/page');
				
				$id = $data[$this->getColumnIndex($headerColumns,'Identifier')];
				$model->load($id);
				
				foreach ($headerColumns as $index => $key) {
					$key = strtolower($key);
					switch ($key) {

						case "stores":
							$stores = $data[$index];
							$storesArray = explode(';', $stores);
							if($behaviour){
								$model->setData($key, $storesArray);
								$model->setData('store_id', $storesArray);
							}
							break;
						
						default:
							if($behaviour)
								$model->setData($key, html_entity_decode($data[$index]));
							break;
						

					}
				}

				try {	
					if($behaviour)
						$model->save();
					else{
						$cmsPageData = array(
								'title' => $data[$this->getColumnIndex($headerColumns,'title')],
								'root_template' => $data[$this->getColumnIndex($headerColumns,'root_template')],
								'meta_keywords' => $data[$this->getColumnIndex($headerColumns,'meta_keywords')],
								'meta_description' => $data[$this->getColumnIndex($headerColumns,'meta_description')],
								'identifier' => $data[$this->getColumnIndex($headerColumns,'identifier')],
								'content_heading' => $data[$this->getColumnIndex($headerColumns,'content_heading')],
								'content' => $data[$this->getColumnIndex($headerColumns,'content')],
								'stores' => $storesArray,
								'creation_time'=> $data[$this->getColumnIndex($headerColumns,'Date_Created')],
								'update_time'=> $data[$this->getColumnIndex($headerColumns,'Last_Updated')],
								'is_active' =>  $data[$this->getColumnIndex($headerColumns,'is_active')],
								'sort_order' =>  $data[$this->getColumnIndex($headerColumns,'sort_order')],
								'layout_update_xml'=>  $data[$this->getColumnIndex($headerColumns,'layout_update_xml')],
								'custom_theme'=>  $data[$this->getColumnIndex($headerColumns,'custom_theme')],
								'custom_root_template'=>  $data[$this->getColumnIndex($headerColumns,'custom_root_template')],
								'custom_layout_update_xml'=> $data[$this->getColumnIndex($headerColumns,'custom_layout_update_xml')],
								'custom_theme_from'=>  $data[$this->getColumnIndex($headerColumns,'custom_theme_from')],
								'custom_theme_to'=>  $data[$this->getColumnIndex($headerColumns,'custom_theme_to')]
							);
					$model->setData($cmsPageData)->save();
				}
					$result++; 
					//Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('Updated ' . $id));
				} catch (Exception $e) {
					//Mage::throwException($e->getMessage() . ' URL Key = ' . $id);
					continue;
					
				}
			
			}
			
		} else {
			throw new Exception('Error opening file ' . $filepath);
		}
		$res = $result.'~'.(($row-1)-$result);
		if(! $result)
			$res = '';
		return $res;
		
	}


	 public function getColumnIndex($header,$column) {
		$index = array_search(strtolower($column),array_map('strtolower', $header));
		return $index;
	}
}
