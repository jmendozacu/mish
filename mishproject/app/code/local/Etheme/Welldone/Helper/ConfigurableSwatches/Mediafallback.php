<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22.07.2015
 * Time: 16:16
 */ 
class Etheme_Welldone_Helper_ConfigurableSwatches_Mediafallback extends Mage_ConfigurableSwatches_Helper_Mediafallback {

    protected function _resizeProductImage($product, $type, $keepFrame, $image = null, $placeholder = false)
    {

        $hasTypeData = $product->hasData($type) && $product->getData($type) != 'no_selection';
        if ($image == 'no_selection') {
            $image = null;
        }
        if ($hasTypeData || $placeholder || $image) {
            $helper = Mage::helper('catalog/image')
                ->init($product, $type, $image)
                ->keepFrame(($hasTypeData || $image) ? $keepFrame : false);
            $keep_aspect_ratio   = Mage::helper('welldone')->getLayoutOption('options/keep_image_aspect_ratio');
            $aspect_ratio_width  = Mage::helper('welldone')->getLayoutOption('options/image_width');
            $aspect_ratio_height = Mage::helper('welldone')->getLayoutOption('options/image_height');

            if ($type == 'small_image') {
                if(!Mage::registry('current_product')) {
                    $w = 263;
                    $h = 330;
                    $helper->constrainOnly(true)->keepFrame(true)->resize($w, $h);
                }else{
                    $w=555*1.5;
                    $h=696*1.5;


                        $helper->constrainOnly(false)->keepAspectRatio(true)->keepFrame(true)->resize($w, $h);

                }

            } else if($type == 'image')
            {

                $w=555*1.5;
                $h=696*1.5;

                if(!$keep_aspect_ratio && !empty($aspect_ratio_width)){
                    $w = Mage::helper('welldone')->getLayoutOption('options/image_width');
                }
                if(!$keep_aspect_ratio && !empty($aspect_ratio_height)){
                    $h = Mage::helper('welldone')->getLayoutOption('options/image_height');
                }
                if($keep_aspect_ratio)
                {
                    $helper->constrainOnly(false)->keepAspectRatio(true)->keepFrame(false)->resize($w, $h);
                }else
                {
                    $helper->constrainOnly(true)->resize($w, $h);
                }
            }
            return (string)$helper;
        }
        return false;
    }
}