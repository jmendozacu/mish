<?php

class VES_VendorsMap_Model_Map extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorsmap/map');
    }
    
    public function upload($file,$id){
    	$path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."tmp".DS."vendor".Mage::getSingleton('vendors/session')->getVendorId().DS.base64_decode($file);
    		
    	$path_move = Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."logo".DS."vendor".Mage::getSingleton('vendors/session')->getVendorId().DS."address".$id.DS;
    		
    	if(is_dir($path_move)==false){
    		mkdir($path_move,0777,true);
    	}
    	
    	$image_new = $path_move.base64_decode($file);

    	rename($path, $image_new);
    }

    public function uploadFileAdmin($file,$id,$vendor_id){
        $path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."tmp".DS."vendor".$vendor_id.DS.base64_decode($file);

        $path_move = Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."logo".DS."vendor".$vendor_id.DS."address".$id.DS;

        if(is_dir($path_move)==false){
            mkdir($path_move,0777,true);
        }

        $image_new = $path_move.base64_decode($file);

        rename($path, $image_new);
    }

    public function getOptionKmMap(){
    	return array(
    			null => Mage::helper('vendorsmap')->__('All'),
    			10   => Mage::helper('vendorsmap')->__('10 Km'),
    			20   => Mage::helper('vendorsmap')->__('20 Km'),
    			50   => Mage::helper('vendorsmap')->__('50 Km'),
    			100   => Mage::helper('vendorsmap')->__('100 km'),
    			500   => Mage::helper('vendorsmap')->__('200 km'),
    	);
    }

    public function getOptionTypeMap(){
        return array(
            1   => Mage::getStoreConfig('vendors/vendors_map/store_attribute_1'),
            2   => Mage::getStoreConfig('vendors/vendors_map/store_attribute_2'),
        );
    }

    public function getTypeNameMap($code){
        $name = null;
        switch($code){
            case 1 :
                $name =  Mage::getStoreConfig('vendors/vendors_map/store_attribute_1');
                break;
            case 2 :
                $name = Mage::getStoreConfig('vendors/vendors_map/store_attribute_2');
                break;
        }
        return $name;
    }
}