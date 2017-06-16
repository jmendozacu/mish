<?PHP
class Eisbehr_QrMage_Model_Qr extends Varien_Object
{
	private $baseurl = "http://chart.apis.google.com/chart";
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function setBaseUrl($url)
	{
		$this->baseurl = $url;
		return $this;
	}
	
	public function getQrImageSrc($url, $size, $level, $margin, $encoding)
	{
		$src  = NULL;
		
		$src .= $this->baseurl;
		$src .= "?chs=" . $size . "x" . $size;
		$src .= "&amp;cht=qr";
		$src .= "&amp;chld=" . $level . "|" . $margin;
		$src .= "&amp;choe=" . $encoding;
		$src .= "&amp;chl=" . $url;
		
		return $src;
	}
}
