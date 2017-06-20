<?php

class Fishpig_SmushIt_Helper_License extends Mage_Core_Helper_Abstract
{
	const MODULE_NAME = 'Fishpig_SmushIt';
	const LICENSE_CODE = '498e54dab60e13d81d540803ca218153';

	public function getLicenseCode()
	{
		return self::LICENSE_CODE;
	}

	public function getImageHtml($format = '')
	{
		return sprintf('<img src="%s" alt="Validating License..." />', $this->getLicenseValidationUrl($format));
	}

	public function getImageJs($format = '', $launcher = null)
	{
		if ($launcher) {
			return sprintf(
				'<div id="imgtarget"></div><script type="text/javascript">window._%sLauncher = false;document.observe("dom:loaded", function() {
 $$("%s").invoke("observe", "click", function(event) {
 if (window._%sLauncher === false) {
 window._%sLauncher = true;
 $("imgtarget").insert(new Element("img", {src: "%s", alt: "Validating License..."}));
 }
 });
 });
 </script>',
				self::MODULE_NAME,
				$launcher,
				self::MODULE_NAME,
				self::MODULE_NAME,
				$this->getLicenseValidationUrl($format)
			);
		}

		return sprintf(
			'<div id="imgtarget"></div><script type="text/javascript">document.observe("dom:loaded", function() {$("imgtarget").insert(new Element("img", {src: "%s", alt: "Validating License..."});});</script>',
			$this->getLicenseValidationUrl($format)
		);
	}

	public function getLicenseValidationUrl($format = '')
	{
		return 'http://license.fishpig.co.uk/2.0/'
		. $this->getLicenseCode()
		. '/'
		. base64_encode(parse_url(Mage::getUrl('', array('_store' => 0, '_nosid' => true)), PHP_URL_HOST))
		. '/'
		. ($format !== '' ? $format : '');
	}

	public function validate()
	{
		/*$httpResult = $this->_makeHttpRequest($this->getLicenseValidationUrl());

		if (is_null($httpResult)) {
			return null;
		}

		$httpResultObject = @json_decode($httpResult, true);

		if (!$httpResultObject) {
			return null;
		}

		if ((int)$httpResultObject['status'] === 0) {
			throw new Exception($httpResultObject['msg']);
		} */

		return true;
	}

	protected function _makeHttpRequest($url)
	{
		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => 'FishPig Licenser (' . self::MODULE_NAME . ')',
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HEADER => false,
			CURLOPT_URL => $url
		));

		$result = @curl_exec($ch);

		if (curl_errno($ch) || curl_error($ch)) {
			return null;
		}

		curl_close($ch);

		return $result;
	}
}

