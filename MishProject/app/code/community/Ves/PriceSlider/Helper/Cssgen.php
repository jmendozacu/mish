<?php

class Ves_PriceSlider_Helper_Cssgen extends Mage_Core_Helper_Abstract
{
	/**
	 * Path and directory of the automatically generated CSS
	 *
	 * @var string
	 */
	protected $_ionSkinCssFolder;
	protected $_ionSkinCssPath;
	protected $_ionSkinCssDir;
	protected $_templatePath;
	
	public function __construct()
	{
		//Create paths
		$theme_name =  Mage::getDesign()->getTheme('frontend');
	    $package = Mage::getSingleton('core/design_package')->getPackageName();
		$this->_ionSkinCssFolder = 'ves_priceslider/ion/';
		$this->_ionSkinCssPath = 'frontend/'.$package.'/'.$theme_name.'/' . $this->_ionSkinCssFolder;
		$this->_ionSkinCssDir = Mage::getBaseDir('skin') . '/' . $this->_ionSkinCssPath;
		$this->_templatePath = 'frontend/'.$package.'/'.$theme_name.'/ves_priceslider/';
	}
	
	/**
	 * Get file path: CSS design
	 *
	 * @return string
	 */
	public function getIonSkinFile()
	{
		$css_ion_skin = Mage::getStoreConfig("ves_priceslider/ajax_conf/range_slider_skin");
		$css_ion_skin = !$css_ion_skin?'skinNice':$css_ion_skin;
		if($css_ion_skin) {
			$css_ion_skin = "ion.rangeSlider.".$css_ion_skin.".css";
			return $this->_ionSkinCssFolder . $css_ion_skin;
		}
		return ;
	}
}
