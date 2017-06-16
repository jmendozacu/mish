<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves FAQ Extension
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FAQ_Model_Import_Question extends Mage_Core_Model_Abstract {

	private $array_delimiter = ';';
	private $delimiter = ',';
	private $header_columns;

	public function process($filepath, $stores = array()) {
		// get the file extension
		$array = pathinfo($filepath);
		switch ($array["extension"] ) {
			case "csv":
			$this->importCsv($filepath, $stores);
			break;
			default:
			Mage::throwException("File is of unknown format, cannot process to import");
			break;
		}
	}

	private function openFile($filepath) {
		$handle = null;
		if (($handle = fopen($filepath, "r")) !== FALSE) {
			return $handle;
		} else {
			throw new Exception('Error opening file ' . $filepath);
		}
	}

	public function restoreArray($default_array = array(), $import_array = array()) {
		if(!empty($import_array)) {
			$tmp = array();
			foreach($import_array as $k=>$v) {
				if(in_array($v, $default_array) || $v == 0) {
					$tmp[] = $v;
				}
			}
		}
		if(empty($tmp)) {
			$tmp = array(0);
		}
		return $tmp;
	}

	public function importCsv($filepath, $stores = array()) {
		$handle = $this->openFile($filepath);
		$row = 0;
		if ( $handle != null ) {
			while (($data = fgetcsv($handle, 110000, $this->delimiter)) !== FALSE) {
				$row++;
				// if this is the head row keep this as a column reference
				if ($row == 1 || empty($this->header_columns)) {
					$this->mapHeader($data);

					continue;
				}
				// make sure we have a reset model object
				$model = Mage::getModel('ves_faq/question');
				$title = "";

				// loop through each column
				foreach ($this->header_columns as $index => $keyname) {
					$keyname = strtolower($keyname);
					$keyname = str_replace(" ", "_", $keyname);
					$import_stores = $stores;
					// switch statement incase we need to do logic depending on the column name
					switch ($keyname) {
						case "question_id":
						break;
						case "title";
						$title = $data[$index];
						$model->setData('title', html_entity_decode($data[$index]));
						break;
						case "stores":
						$tmp_stores = $data[$index];
						$stores_array = explode('-', $tmp_stores);
						$import_stores = $this->restoreArray($stores, $stores_array);
						if(!empty($import_stores)) {
							$model->setData('stores', $import_stores);
						}
						break;
						default:
						$model->setData($keyname, html_entity_decode($data[$index]));
						break;
					} // end switch
				} // end foreach

				// save our block
				try {
					$model->save();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ves_faq')->__('Updated: ' . $title));
				} catch (Exception $e) {
					Mage::throwException($e->getMessage() . ' Question = ' . $data[$this->getTitleColumnIndex()]);
				}
			}
		}
	}

	private function mapHeader($data_array) {
		$this->header_columns = array();
		if(count($data_array) >= 1) {
			if(count($data_array) > 1 || (count($data_array) == 1 && $data_array[0] )) {
				$this->header_columns = $data_array;
			}
		}


	}

	private function getTitleColumnIndex() {
		$header = $this->header_columns;
		$index = array_search('Title', $header);
		return $index;
	}
}