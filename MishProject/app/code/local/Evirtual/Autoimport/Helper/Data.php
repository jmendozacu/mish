<?php 
/**
 * Evirtual_Autoimport extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Evirtual
 * @package		Evirtual_Autoimport
 * @copyright  	Copyright (c) 2013
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Autoimport default helper
 *
 * @category	Evirtual
 * @package		Evirtual_Autoimport
 * @author Ultimate Module Creator
 */
class Evirtual_Autoimport_Helper_Data extends Mage_Core_Helper_Abstract{
	
	protected function initlib(){
		require_once Mage::getBaseDir('lib').'\evirtual\xlsx\simplexlsx.class.php';	
	}
	
	public function ArrayData($url){
		
		$this->initlib();
		
		$localfile=explode("http://",$url);
		$localfile=str_replace(".","_",$localfile[1]);
		$localfile=str_replace("/","_",$localfile);
		$localfile=Mage::getBaseDir('var')."/evirtual/tmp/".$localfile;
		
		/*return $localfile;
		
		exit;*/
		
		$localfile=str_replace("_xlsx",".xlsx",$localfile);
		
		
		if(!file_exists($localfile)){
			copy($url, $localfile);
		}
		/*Zend_Debug::dump($localfile);
		exit;*/
		$xlsx = new SimpleXLSX($localfile);
		
		//$ret = $vdc->service_getSupportedFunctions();
		
		//$ret=$this->json_pretty_print($ret);
		//Mage::register('vehicaldatajson', $vdc);
		
		return  $xlsx->rows();	
	}
	
}