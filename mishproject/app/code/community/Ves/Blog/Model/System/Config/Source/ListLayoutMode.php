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
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Blog_Model_System_Config_Source_ListLayoutMode
{

  public function toOptionArray()
  {

    $output = array();
    $output[] = array("value"=>"" , "label" => Mage::helper('adminhtml')->__("Default Layout"));
    $output[] = array("value"=>"list" , "label" => Mage::helper('adminhtml')->__("List - Large Image Layout"));
    $output[] = array("value"=>"grid" , "label" => Mage::helper('adminhtml')->__("Grid Layout"));
    $output[] = array("value"=>"second" , "label" => Mage::helper('adminhtml')->__("Second - Small Image Layout"));
    $output[] = array("value"=>"masonry" , "label" => Mage::helper('adminhtml')->__("Masonry Layout"));
    $output[] = array("value"=>"custom" , "label" => Mage::helper('adminhtml')->__("Custom Layout"));
    $output[] = array("value"=>"thumb_view" , "label" => Mage::helper('adminhtml')->__("Thumb view layout"));

    return $output ;
  }
}