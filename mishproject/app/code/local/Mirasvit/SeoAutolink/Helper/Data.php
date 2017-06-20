<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.1.0
 * @revision  556
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_SeoAutolink_Helper_Data extends Mage_Core_Helper_Abstract
{
	private $links;

	public function getLinks($text) {
		Varien_Profiler::start('seoautolink_getLinks');
		$links = Mage::getModel('seoautolink/link')->getCollection()
					->addActiveFilter()
					->addStoreFilter(Mage::app()->getStore())
					;
		@$links->getSelect()
			->where("lower(?) LIKE CONCAT('%', lower(keyword), '%')", $text)
			->order( array('LENGTH(main_table.keyword) desc') ); //we need to replace long keywords firstly
		Varien_Profiler::stop('seoautolink_getLinks');
		// echo $links->getSelect();die;
		return $links;
	}

	public function addLinks($text) {
		if (strpos(Mage::helper('core/url')->getCurrentUrl(), '/checkout/onepage/')) {
            return $text;
        }
		return $this->_addLinks($text, $this->getLinks($text));
	}

	public function _addLinks($text, $links) {
		Varien_Profiler::start('seoautolink_addLinks');
		foreach ($links  as $link) {
			$nofollow = $link->getIsNofollow()?'rel=\'nofollow\'':'';

			$target = $link->getUrlTarget()?" target='{$link->getUrlTarget()}' ":"";
			$html = "<a href='{$link->getUrl()}'$target $nofollow>----->{$link->getKeyword()}</a>";//-----> Ð½Ð°Ð¼ Ð½ÑÐ¶Ð½Ð¾, ÑÑÐ¾Ð± Ð² Ð´Ð°Ð»ÑÐ½ÐµÑÐµÐ¼ Ð¿Ð¾Ð´Ð¼ÐµÐ½Ð¸ÑÑ ÐºÐ»ÑÑÐµÐ²Ð¾Ðµ ÑÐ»Ð¾Ð²Ð¾, Ð½Ð° ÑÑÐ¾ Ð¶Ðµ ÑÐ»Ð¾Ð²Ð¾ Ñ Ð¿ÑÐ°Ð²Ð¸Ð»ÑÐ½ÑÐ¼ ÑÐµÐ³Ð¸ÑÑÑÐ¾Ð¼
			$keyword = $link->getKeyword();
			$maxReplacements = -1;
			if ($link->getMaxReplacements() > 0) {
				$maxReplacements = $link->getMaxReplacements();
			}
			$direction = 0;
			switch ($link->getOccurence()){
				case Mirasvit_SeoAutolink_Model_Config_Source_Occurence::FIRST:
					$direction = 0;
					break;
				case Mirasvit_SeoAutolink_Model_Config_Source_Occurence::LAST:
					$direction = 1;
					break;
				case Mirasvit_SeoAutolink_Model_Config_Source_Occurence::RANDOM:
					$direction = rand(0,1);
					break;
			}
			if ($direction == 1) {
				$text = $this->strrev($text);
				$html = $this->strrev($html);
				$keyword = $this->strrev($keyword);
			}
			//~ $pattern  = '#\b(' . str_replace('#', '\#', $keyword) . ')\b#iu';
			//~ $tokenized_text = preg_replace("{$pattern}", $html, $tokenized_text, $maxReplacements);
			$text = $this->replace($keyword, $html, $text, $maxReplacements);

			if ($direction == 1) {
				$text = $this->strrev($text);
				$keyword = $this->strrev($keyword);
				$text = $this->str_replace('----->'.$keyword, $keyword, $text);
			}
		}
   		Varien_Profiler::stop('seoautolink_addLinks');
		return $text;
	}
	//replace words and left the same cases
	protected function replace($find, $replace, $source, $maxReplacements) {
		// substitute some special html characters with their 'real' value
		$searchT = array('&Eacute;',
		                  '&Euml;',
		                  '&Oacute;',
		                  '&eacute;',
		                  '&euml;',
		                  '&oacute;',
		                  '&Agrave;',
		                  '&Egrave;',
		                  '&Igrave;',
		                  '&Iacute;',
		                  '&Iuml;',
		                  '&Ograve;',
		                  '&Ugrave;',
		                  '&agrave;',
		                  '&egrave;',
		                  '&igrave;',
		                  '&iacute;',
		                  '&iuml;',
		                  '&ograve;',
		                  '&ugrave;',
		                  '&Ccedil;',
		                  '&ccedil;'
		                 );
		$replaceT = array('Ã',
		                   'Ã',
		                   'Ã',
		                   'Ã©',
		                   'Ã«',
		                   'Ã³',
		                   'Ã',
		                   'Ã',
		                   'Ã',
		                   'Ã',
		                   'Ã',
		                   'Ã',
		                   'Ã',
		                   'Ã ',
		                   'Ã¨',
		                   'Ã¬',
		                   'Ã­',
		                   'Ã¯',
		                   'Ã²',
		                   'Ã¹',
		                   'Ã',
		                   'Ã§'
		                  );

		$source = $this->str_replace($searchT, $replaceT, $source);

		  // matches for these expressions will be replaced with a unique placeholder
		$preg_patterns = array(
		      '#<!--.*?-->#s'       // html comments
		    , '#<a[^>]*>.*?</a>#si' // html links
		    , '#<[^>]+>#'           // generic html tag
		    //~ , '#&[^;]+;#'           // special html characters
		    //~ , '#[^ÃÃÃÃ©Ã«Ã³ÃÃÃÃÃÃÃÃ Ã¨Ã¬Ã­Ã¯Ã²Ã¹ÃÃ§\w\s]+#'   // all non alfanumeric characters, spaces and some special characters
		);

		$pl = new Mirasvit_TextPlaceholder($source, $preg_patterns);
		// raw text, void of any html.
		$source = $pl->get_tokenized_text();
		if ($maxReplacements == -1) {
			$maxReplacements = 9999999;
		}
		$pos = 0;

		for ($i=0; $i< $maxReplacements; $i++) {
			//Ð¸ÑÐµÐ¼ Ð¿Ð¾Ð·Ð¸ÑÐ¸Ñ Ð½Ð°ÑÐµÐ³Ð¾ ÐºÐ»ÑÑÐµÐ²Ð¾Ð³Ð¾ ÑÐ»Ð¾Ð²Ð°
			$pos = $this->stripos($source, $find, $pos);

			if ($pos === false) { //ÐµÑÐ»Ð¸ Ð½ÐµÑ, Ð¾ÑÑÐ°Ð½Ð°Ð²Ð»Ð¸Ð²Ð°ÐµÐ¼ÑÑ
				break;
			} else {
				$char = $this->get_char($source, $pos-1); //Ð¿Ð¾Ð»ÑÑÐ°ÐµÐ¼ Ð¿ÑÐµÐ´ÑÐ´ÑÑÐ¸Ð¹ ÑÐ¸Ð¼Ð²Ð¾Ð»
				if ($char && $this->is_alnum($char)) { //ÐµÑÐ»Ð¸ Ð¿ÑÐµÐ´ÑÐ´ÑÑÐ¸Ð¹ ÑÐ¸Ð¼Ð²Ð¾Ð» Ð±ÑÐºÐ²Ð°, ÑÐ¾ Ð¼Ñ Ð¿ÑÐ¾Ð¿ÑÑÐºÐ°ÐµÐ¼ ÑÑÐ¾ Ð²ÑÐ¾Ð¶Ð´ÐµÐ½Ð¸Ðµ ÑÐ»Ð¾Ð²Ð°
					$pos += $this->strlen($find);
					continue;
				}
				$char = $this->get_char($source, $pos+$this->strlen($find));
				if ($char && $this->is_alnum($char)) {  //ÐµÑÐ»Ð¸ ÑÐ¸Ð¼Ð²Ð¾Ð» Ð¿Ð¾ÑÐ»Ðµ Ð½Ð°ÑÐµÐ³Ð¾ ÑÐ»Ð¾Ð²Ð° - Ð±ÑÐºÐ²Ð°, ÑÐ¾ Ð¼Ñ Ð¿ÑÐ¾Ð¿ÑÑÐºÐ°ÐµÐ¼ ÑÑÐ¾ Ð²ÑÐ¾Ð¶Ð´ÐµÐ½Ð¸Ðµ ÑÐ»Ð¾Ð²Ð°
					$pos += $this->strlen($find);
					continue;
				}
			}

			$found = $this->substr($source, $pos, $this->strlen($find));
			//ÑÐ¾Ð·Ð´Ð°ÐµÐ¼ ÑÑÑÐ¾ÐºÑ, Ð½Ð° ÐºÐ¾ÑÐ¾ÑÑÑ Ð¼Ñ Ð±ÑÐ´ÐµÐ¼ Ð² Ð´Ð°Ð»ÑÐ½ÐµÐ¹ÑÐµÐ¼ Ð¼ÐµÐ½ÑÑÑ ÑÐ»Ð¾Ð²Ð°, Ñ Ð¿ÑÐ°Ð²Ð¸Ð»ÑÐ½ÑÐ¼ ÑÐµÐ³Ð¸ÑÑÑÐ¾Ð¼ ÑÐ»Ð¾Ð²;
			$replaceWithCorrectCase =  $this->str_replace('----->'.$find, $found, $replace); //-----> Ð¼Ñ Ð²ÑÑÑÐ²Ð»ÑÐ»Ð¸ ÑÐ°Ð½Ð½ÐµÐµ Ð¿ÑÐ¸ ÑÐ¾ÑÐ¼Ð¸ÑÐ¾Ð²Ð°Ð½Ð¸Ð¸ ÑÑÑÐ»ÐºÐ¸ Ð´Ð»Ñ ÑÑÐ¾Ð³Ð¾ ÑÐ»ÑÑÐ°Ñ
			//Ð·Ð°Ð¼ÐµÐ½ÑÐµÐ¼ ÑÐ»Ð¾Ð²Ð° Ð² Ð¸ÑÑÐ¾Ð´Ð½Ð¾Ð¼ ÑÐµÐºÑÑÐµ
			// $source = substr_replace($source, $replaceWithCorrectCase, $pos, strlen($find));
			$source = $this->substr_replace($source, $replaceWithCorrectCase, $pos, $this->strlen($find));
			$pos += $this->strlen($replaceWithCorrectCase);
		}

		// we will later need this to put the html we stripped out, back in.
		$translation_table = $pl->get_translation_table();
		// reconstruct the original text (now with links)
		foreach ($translation_table as $key=>$value)
		{
			$source = $this->str_replace($key, $value, $source);
		}
		// replace the special html characters
		$source = $this->str_replace($replaceT, $searchT, $source);
		return $source;
	}

	/*
	* analog of ctype_alnum, but with support of Cyrillic
	*/
	public function is_alnum($string) {
		return (bool)preg_match("/^[a-zA-Z\p{Cyrillic}0-9]+$/u", $string);
	}

	public function strlen($string) {
		return Mage::helper('core/string')->strlen($string);
	}

	public function substr($string, $offset, $length = null) {
		return Mage::helper('core/string')->substr($string, $offset, $length);
	}

	public function substr_replace($output, $replace, $posOpen, $posClose) {
        return substr_replace($output, $replace, $posOpen, $posClose);
    }

    public function stripos($source, $find, $pos = null) {
		if (extension_loaded('mbstring')) {
			$pos = Mage::helper('core/string')->strpos(mb_convert_case($source, MB_CASE_LOWER, "UTF-8"),  mb_convert_case($find, MB_CASE_LOWER, "UTF-8"), $pos);
		} else {
			$pos = stripos($source, $find, $pos);
		}
    	return $pos;
    }

    public function get_char($source, $pos) {
    	if ($pos < 0 || $pos >= $this->strlen($source)) {
    		return false;
    	}
    	return $this->substr($source, $pos, 1);
    }

    public function str_replace($search, $replace, $source) {
    	return str_replace($search, $replace, $source);
    }

    public function strrev($string) {
    	return Mage::helper('core/string')->strrev($string);
    }
}

class Mirasvit_TextPlaceholder {

    var $_translation_table = array();

    function __construct($text, $patterns) {
        $this->_tokenized_text = preg_replace_callback($patterns, array($this, 'placeholder'), $text);
    }

    function get_translation_table() {
        return $this->_translation_table;
    }

    function get_tokenized_text() {
        return $this->_tokenized_text;
    }

    function placeholder($matches) {
        $sequence = count($this->_translation_table);
        $placeholder = ' xkjndsfkjnakcx' . $sequence . 'cxmkmweof329jc ';
        $this->_translation_table[$placeholder] = $matches[0];
        return $placeholder;
    }

}
