<?PHP
class Eisbehr_QrMage_CodeController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$config = Mage::helper('qrmage/config');
		
		// $url      = Mage::helper('core/url')->getCurrentUrl();
		$url      = NULL;
		$size     = $config->getSwetakeSize();
		$image    = $config->getSwetakeImage();
		$level    = $config->getLevel();
		
		$dataPath  = Mage::getBaseDir('media'); 
		$dataPath .= "/qrmage";
		
		$hash = $this->getRequest()->getParam('hash');
		
		if( preg_match("/^[a-f0-9]{32}$/", $hash) )
		{
			$model   = Mage::getModel('qrmage/db');
			$address = $model->getEntry($hash);
			$url     = $address;
		}
		
		$helper = Mage::helper('qrmage/swetake');
		
		$helper->setQrCodeDataString($url)
			   ->setConfigDataPath($dataPath)
			   ->setQrCodeErrorCorrect($level)
			   ->setQrCodeImageType($image)
			   ->setQrCodeModuleSize($size);
		
		$helper->createQrCode();
		
		return $this;
	}
}