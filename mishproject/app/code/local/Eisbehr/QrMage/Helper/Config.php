<?php
class Eisbehr_QrMage_Helper_Config extends Mage_Core_Helper_Abstract
{
	private $config;
	
	public function load()
	{
		$this->config["main"]["level"] 		= array('L', 'M', 'Q', 'H');
		$this->config["main"]["encoding"] 	= array('UTF-8', 'Shift_JIS', 'ISO-8859-1');
		
		return $this;
	}
	
	public function getLevelConfig()
	{
		return $this->config["main"]["level"];
	}
	
	public function getEncodingConfig()
	{
		return $this->config["main"]["encoding"];
	}
	
	/* adminhtml */
	
	public function configValue($path, $name)
	{
		$basePath  = 'qrmage/';
		$basePath .= $path;
		
		if( substr($basePath, 0, -1) != '/' )
		{
			$basePath .= '/';
		}
		
		return Mage::getStoreConfig( $basePath . $name );
	}
	
	/* general */
	
	public function getActive()
	{
		$value = $this->configValue('qrmage', 'active');
		return ( $value ) ? true : false ;
	}
	
	public function getEngine()
	{
		$value = $this->configValue('qrmage', 'engine');
		
		if( empty($value) )
		{
			$value = "google";
		}
		
		return $value;
	}
	
	public function getLevel()
	{
		$value = $this->configValue('qrmage', 'level');
		
		if( empty($value) )
		{
			$value = "L";
		}
		
		return $value;
	}
	
	public function getLabel()
	{
		$value = $this->configValue('qrmage', 'label');
		return $value;
	}
	
	/* google */
	
	public function getGoogleSize()
	{
		$value = $this->configValue('google', 'size');
		return $value;
	}
	
	public function getGoogleMargin()
	{
		$value = $this->configValue('google', 'margin');
		return $value;
	}
	
	public function getGoogleEncoding()
	{
		$value = $this->configValue('google', 'encoding');
		return $value;
	}
	
	/* swetake */
	
	public function getSwetakeSize()
	{
		$value = $this->configValue('swetake', 'size');
		
		if( empty($value) )
		{
			$value = 4;
		}
		
		return $value;
	}
	
	public function getSwetakeImage()
	{
		$value = $this->configValue('swetake', 'image');
		
		if( empty($value) )
		{
			$value = "p";
		}
		
		return $value;
	}
}
