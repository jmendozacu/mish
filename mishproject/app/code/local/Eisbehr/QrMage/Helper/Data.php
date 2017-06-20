<?php
// Information:
// http://code.google.com/apis/chart/

class Eisbehr_QrMage_Helper_Data extends Mage_Core_Helper_Abstract
{
	/* vars */
	
	private $_url      = NULL;
	private $_engine   = "google";
	private $_size     = 150;
	private $_level    = "L";
	private $_margin   = 4;
	private $_encoding = "UTF-8";
	private $_label    = "QR Code";
	
	/* setter */
	
	public function setUrl($url)
	{
		$this->_url = urlencode($url);
		return $this;
	}
	
	public function setEngine($engine)
	{
		$this->_engine = urlencode($engine);
		return $this;
	}
	
	public function setSize($size)
	{
		if( is_numeric($size) && $size <= 500 )
		{
			$this->_size = $size;
		}
		
		return $this;
	}
	
	public function setLevel($level)
	{
		$config = Mage::helper('qrmage/config')->load();
		$config = $config->getLevelConfig();
		
		if( in_array($level, $config) )
		{
			$this->_level = $level;
		}
		
		return $this;
	}
	
	public function setMargin($margin)
	{
		if( is_numeric($margin) )
		{
			$this->_margin = $margin;
		}
		
		return $this;
	}
	
	public function setEncoding($encoding)
	{
		$config = Mage::helper('qrmage/config')->load();
		$config = $config->getEncodingConfig();
		
		if( in_array($encoding, $config) )
		{
			$this->_encoding = $encoding;
		}
		
		return $this;
	}
	
	public function setLabel($label)
	{
		$this->_label = $label;
		return $this;
	}
	
	/* getter */
	
	public function getQrImageHtml($url = NULL) 
	{
		if( $url != NULL )
		{
			$this->setUrl($url);
		}
		
		$src   = NULL;
		
		if( $this->_engine == "google" )
		{
			$model = Mage::getModel('qrmage/qr');
			$src = $model->getQrImageSrc($this->_url, 
										 $this->_size, 
										 $this->_level, 
										 $this->_margin, 
										 $this->_encoding);
		}
		else
		{
			$model = Mage::getModel('qrmage/db');
			$hash = $model->insertEntry($this->_url);
			
			$src  = Mage::getUrl('qrmage/code');
			$src .= "?hash=" . $hash;
		}
		
		$html  = NULL;
		
		$html .= "<img src=\"";
		$html .= $src;
		$html .= "\"";
		
		if( !empty($this->_label) )
		{
			$html .= " alt=\"" . $this->_label . "\"";
			$html .= " title=\"" . $this->_label . "\"";
		}
		
		if( $this->_engine == "google" )
		{
			$html .= " width=\"" . $this->_size . "\"";
			$html .= " height=\"" . $this->_size . "\"";
		}
		
		$html .= " />\n";
		
		return $html;
	}
}
