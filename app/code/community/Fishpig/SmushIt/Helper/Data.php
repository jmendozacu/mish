<?php

class Fishpig_SmushIt_Helper_Data extends Mage_Core_Helper_Abstract
{
	const API_URL = 'http://www.resmush.it/ws.php';
	const META_FILE_POSTFIX = '.meta';

	public function run()
	{
		// if (!$this->isEnabled()) {
		// 	return $this->_log($this->__('The Smush.it extension is not enabled.'));
		// }

		// if (!$this->isValidLicenseCode()) {
		// 	return $this->_log($this->__('Invalid license code.'));
		// }  

		Mage::getResourceSingleton('smushit/image')->installDatabaseTables();

		$source = $this->getSourceDirectory();

		if (!is_dir($source)) {
			@mkdir($source, 0777, true);

			if (!is_dir($source)) {
				return $this->_log($this->__('Your product image cache directory (%s) does not exist.', $source));
			}
		}

		$files = $this->scanDirectory($source, $this->getLimit());

		if (!$files) {
			return $this->_log($this->__('Your product image cache directory (%s) is empty or all images have been optimised.', $source));
		}

		foreach ($files as $file) {
			try {
				$this->smushFile($file);
			} catch (Exception $e) {
				$this->_logForFile($file, $e->getMessage());
				throw $e;
			}
		}

		return $this;
	}

	public function smushFile($file)
	{
		if (!is_writable($file)) {
			throw new Exception($file . ' is not writable.');
		}

		if (!is_writable(dirname($file))) {
			throw new Exception(dirname($file) . ' is not writable.');
		}

		$result = @json_decode($this->_curlRequest(self::API_URL, array(
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => array('files' => $this->_curlFileCreate($file)),
		)), true);

		if (!$result) {
			throw new Exception('No response from the API');
		}

		$result = new Varien_Object((array)$result);

		if (!$result->getPercent()) {
			@file_put_contents($file . self::META_FILE_POSTFIX, json_encode($result->getData()));
			Mage::getResourceModel('smushit/image')->createUsingResult($file, $result->getData());

			return $this;
		}


		$newFile = $this->_curlRequest($result->getDest());

		if (!$newFile) {
			throw new Exception('Unable to download optimised file.');
		}

		@file_put_contents($file, $newFile);
		@file_put_contents($file . self::META_FILE_POSTFIX, json_encode($result->getData()));

		Mage::getResourceModel('smushit/image')->createUsingResult($file, $result->getData());

		$this->_logForFile($file, $result->getPercent() . '% savings.');

		return $this;
	}

	protected function _curlFileCreate($file)
	{
		if (function_exists('curl_file_create')) {
			if (function_exists('exif_imagetype')) {
				return curl_file_create($file, exif_imagetype($file), basename($file));
			}

			$imageData = getimagesize($file);

			return curl_file_create($file, $imageData[2], basename($file));
		}

		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mime = finfo_file($finfo, $file);
			finfo_close($finfo);

			if (strpos($mime, ';') !== false) {
				$mime = substr($mime, 0, strpos($mime, ';'));
			}
		}

		return "@$file;filename=" . basename($file) . ";type=$mime";
	}

	protected function _curlRequest($url, array $params = array())
	{
		$params += array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => 'Smush.it/Magento',
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HEADER => false,
			CURLOPT_URL => $url
		);

		$ch = curl_init();

		curl_setopt_array($ch, $params);

		$result = @curl_exec($ch);

		if (curl_errno($ch) || curl_error($ch)) {
			curl_close($ch);

			throw new Exception(Mage::helper('wordpress')->__('CURL (%s): %s', curl_errno($ch), curl_error($ch)));
		}

		curl_close($ch);

		return $result;
	}

	public function getSourceDirectory()
	{
		return Mage::getBaseDir() . DS . 'media' . DS . 'catalog' . DS . 'product' . DS . 'cache' . DS;
	}

	public function scanDirectory($dir, $limit = 0)
	{
		$files = array();

		foreach (scandir($dir) as $file) {
			if (trim($file, '.') === '') {
				continue;
			}

			if (strpos($file, self::META_FILE_POSTFIX) !== false) {
				continue;
			}

			$tmp = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;

			if (!is_dir($tmp)) {
				if (!is_file($tmp . self::META_FILE_POSTFIX)) {
					$files[] = $tmp;
				}
			} else {
				$files = array_merge($files, $this->scanDirectory($tmp, $limit));
			}

			if ($limit > 0 && count($files) > $limit) {
				break;
			}
		}

		if ($limit > 0 && count($files) > $limit) {
			return array_slice($files, 0, $limit);
		}

		return $files;
	}

	protected function _logForFile($file, $msg)
	{
		return $this->_log(substr($file, strlen(Mage::getBaseDir('media') + 1)) . ' - ' . $msg);
	}

	protected function _log($msg)
	{
		Mage::log(
			$msg,
			null,
			'smushit.log',
			true
		);

		return $this;
	}

	public function getLimit()
	{
		return (int)Mage::getStoreConfig('smushit/settings/limit');
	}

	public function isEnabled()
	{
		return Mage::getStoreConfigFlag('smushit/settings/enabled');
	}

	public function isValidLicenseCode()
	{
		Mage::helper('smushit/license')->validate();

		return true;
	}
}
