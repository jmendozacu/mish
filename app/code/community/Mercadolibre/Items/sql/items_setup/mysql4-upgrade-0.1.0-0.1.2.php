<?php 
try {
	
	//Initilize logger model
	$commonModel = Mage::getModel('items/common');
	$db = Mage::getSingleton('core/resource')->getConnection('core_write');
	$filename = $commonModel->forwardSlashToBackSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category\meli_categories.csv');
	$sql = "LOAD DATA  INFILE '".$filename."' INTO TABLE `mercadolibre_categories` character set UTF8 FIELDS TERMINATED BY ',' enclosed by '\"' lines terminated by '\r\n'";
	$db->query($sql);


	// Load  mercadolibre_category_attributes data  
	$arrributeDir = $commonModel->forwardSlashToBackSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes');
	$arrributeFileName = $commonModel->forwardSlashToBackSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes\mercadolibre_category_attributes.csv.zip');
	$zip = new ZipArchive;
	if ($zip->open($arrributeFileName) === TRUE) {
		$zip->extractTo($arrributeDir);
		$zip->close();
	}
	
	$filenameArrribute = $commonModel->forwardSlashToBackSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes\mercadolibre_category_attributes.csv');
	$sql = "LOAD DATA  INFILE '".$filenameArrribute."' INTO TABLE `mercadolibre_category_attributes` character set UTF8 FIELDS TERMINATED BY ',' enclosed by '\"' lines terminated by '\r\n'";
	$db->query($sql);
	
	// Load  mercadolibre_category_attribute_values data  
	
	$arrributeValDir = $commonModel->forwardSlashToBackSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes');
	$filenameAttValueZip = $commonModel->forwardSlashToBackSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes\mercadolibre_category_attribute_values.csv.zip');
	@chmod($arrributeValDir,0777);
	$zip = new ZipArchive;
	if ($zip->open($filenameAttValueZip) === TRUE) {
		$zip->extractTo($arrributeValDir);
		$zip->close();
	}
	
	$filenameAttValue = $commonModel->forwardSlashToBackSlash(Mage::getBaseDir('code').DS.'community\Mercadolibre\dump\category-attributes\mercadolibre_category_attribute_values.csv');
	$sql = "LOAD DATA  INFILE '".$filenameAttValue."' INTO TABLE `mercadolibre_category_attribute_values` character set UTF8 FIELDS TERMINATED BY ',' enclosed by '\"' lines terminated by '\r\n'";
	$db->query($sql);
	
	@unlink($filenameArrribute);
	@unlink($filenameAttValue);
	
	
	$melicategoriesModel = Mage::getModel('items/melicategories');
	$service_url = 'https://api.Mercadolibre.com/sites/MLA/categories/all';
	$x_content_created = $melicategoriesModel->getMLXContentCreated($service_url);
	$runDateTime = date('Y-m-d h:i:s', time());
	$melicategoryupdate = Mage::getModel('items/melicategoryupdate');
	$melicategoryupdate->setCreatedDatetime($x_content_created);
	$melicategoryupdate->setRunDatetime($runDateTime);
	$melicategoryupdate->save();
	$db->query("UPDATE core_config_data  set value = '".$x_content_created."' where path='mlitems/categoriesupdateinformation/contentcreationdate'"); 
	$db->query("UPDATE core_config_data  set value = '".$runDateTime."' where path='mlitems/categoriesupdateinformation/lastrundata'");

} catch (Exception $e) {
print_r($e);
    die;
}