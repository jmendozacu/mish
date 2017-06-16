<?PHP
class Eisbehr_QrMage_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$config = Mage::helper('qrmage/config');
		
		$url      = Mage::helper('core/url')->getCurrentUrl();
		$engine   = $config->getEngine();
		$size     = $config->getGoogleSize();
		$level    = $config->getLevel();
		$margin   = $config->getGoogleMargin();
		$encoding = $config->getGoogleEncoding();
		$label    = $config->getLabel();
		
		$helper = Mage::helper('qrmage');
		$html   = $helper->setUrl($url)
						 ->setEngine($engine)
						 ->setSize($size)
						 ->setLevel($level)
						 ->setMargin($margin)
						 ->setEncoding($encoding)
						 ->setLabel($label)
						 ->getQrImageHtml();
		
		echo $html;
		return;
	}
}