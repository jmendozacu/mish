<?php

class VES_VendorsCms_Model_Source_Theme extends Mage_Core_Model_Design_Source_Design
{
	public function toOptionArray()
    {
    	$options = array();
    	$options[] = array(
        	'label' => Mage::helper('vendorscms')->__('Same as main website'),
        	'value' =>  ''
		);
		if(!Mage::helper('vendorscms')->moduleEnable()) return $options;
		
		$availableThemes 	= explode(",",Mage::getStoreConfig('vendors/vendor_page/global_theme'));
		$additionalOptions 	= $this->getAllOptions(false);
		foreach($additionalOptions as $pIndex=>$packages){
			$additionalOptions[$pIndex]['label'] = uc_words($additionalOptions[$pIndex]['label']);
			foreach($packages['value'] as $dIndex=>$design){
				$additionalOptions[$pIndex]['value'][$dIndex]['label'] = uc_words($additionalOptions[$pIndex]['value'][$dIndex]['label']);
				if(!in_array($design['value'], $availableThemes)){
					unset($additionalOptions[$pIndex]['value'][$dIndex]);
					if(!sizeof($additionalOptions[$pIndex]['value'])){
						unset($additionalOptions[$pIndex]);
					}
				}
			}
		}
		return array_merge($options,$additionalOptions);
    }
}