<?php

class VES_BannerManager_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * Is Module Enable
     */
    public function moduleEnable(){
        $result = new Varien_Object(array('module_enable'=>true));
        Mage::dispatchEvent('ves_banner_module_enable',array('result'=>$result));
        return $result->getData('module_enable');
    }

	
	public function getEffectOptionsData()
    {
        $data = array();
        
        $data[] = array(
            'value' => 'random', 
            'label' => Mage::helper('bannermanager')->__('Random')
        );
        $data[] = array(
            'value' =>'sliceDown', 
            'label' => Mage::helper('bannermanager')->__('Slice Down'));
        
        $data[] = array(
            'value' => 'sliceDownLeft', 
            'label' => Mage::helper('bannermanager')->__('Slice Down Left'));
        $data[] = array(
            'value' => 'sliceDownRight',
            'label' => Mage::helper('bannermanager')->__('Slice Down Right'));
        $data[] = array(
            'value' => 'sliceUp', 
            'label' => Mage::helper('bannermanager')->__('Slice Up'));
        $data[] = array(
            'value' => 'sliceUpLeft', 
            'label' => Mage::helper('bannermanager')->__('Slice Up Left'));
        $data[] = array(
            'value' => 'sliceUpRight',
            'label' => Mage::helper('bannermanager')->__('Slice Up Right'));
        
        $data[] = array(
            'value' => 'sliceUpDown', 
            'label' => Mage::helper('bannermanager')->__('Slice Up Down'));
        $data[] = array(
            'value' => 'sliceUpDownLeft', 
            'label' => Mage::helper('bannermanager')->__('Slice Up Down Left'));
        
        $data[] = array(
            'value' => 'sliceUpDownRight',
            'label' => Mage::helper('bannermanager')->__('Slice Up Down Right'));
        $data[] = array(
            'value' => 'fold', 
                'label' => Mage::helper('bannermanager')->__('Fold'));
        $data[] = array(
            'value' => 'fade', 
            'label' =>  Mage::helper('bannermanager')->__('Fade'));
        $data[] = array(
            'value' => 'slideInRight', 
            'label' => Mage::helper('bannermanager')->__('Slide In Right'));
        $data[] = array(
            'value' => 'slideInLeft', 
            'label' => Mage::helper('bannermanager')->__('Slide In Left'));
        $data[] = array(
            'value' => 'boxRandom', 
            'label' => Mage::helper('bannermanager')->__('Box Random'));
        $data[] = array(
            'value' => 'boxRain', 
            'label' => Mage::helper('bannermanager')->__('Box Rain'));
        $data[] = array(
            'value' => 'boxRainReverse', 
            'label' => Mage::helper('bannermanager')->__('Box Rain Reverse'));
        $data[] = array(
            'value' => 'boxRainGrow', 
            'label' => Mage::helper('bannermanager')->__('Box Rain Grow'));
        $data[] = array(
            'value' => 'boxRainGrowReverse', 
            'label' => Mage::helper('bannermanager')->__('Box Rain Grow Reverse'));
        
        return $data;
    }
}