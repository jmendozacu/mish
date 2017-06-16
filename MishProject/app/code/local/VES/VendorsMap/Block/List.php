<?php

class VES_VendorsMap_Block_List extends Mage_Core_Block_Template
{
	protected $_map_collection;

	public function getVendorInfo() {
		return Mage::getModel('vendors/vendor')->loadByVendorId(Mage::registry('vendor_id'));
	}
	
	public function _prepareLayout()
	{
		parent::_prepareLayout();	
	 	if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $vendorId = Mage::registry('vendor_id');
            $breadcrumbsBlock->addCrumb('vendor', array(
            		'label'=>Mage::registry('vendor')->getTitle(),
            		'title'=>Mage::registry('vendor')->getTitle(),
            		'link'=>Mage::helper('vendorspage')->getUrl($vendorId),
            ));

            $breadcrumbsBlock->addCrumb('vendor_reviews', array(
            		'label'=>$this->__('View On Map'),
            		'title'=>$this->__('View On Map'),
            ));

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle('View On Map - ' .Mage::registry('vendor')->getTitle());
            }
        }
	}
	/**
	 * Get review collection of current vendor
	 */
	public function getMapCollection(){
		if(!isset($this->_map_collection)){
			$this->_map_collection = Mage::getModel('vendorsmap/map')->getCollection()
			->addFieldToFilter('vendor_id',$this->getVendorId());
		}
		return $this->_map_collection;
	}
	
	/**
	 * Get current vendor
	 * @return VES_Vendors_Model_Vendor
	 */
	public function getVendor(){
		return Mage::registry('vendor');
	}
	
	/**
	 * Get current vendor id
	 * @return int
	 */
	public function getVendorId() {
		return $this->getVendor()->getId();
	}
	
	public function getDefaultLocationLat(){
		$location = Mage::getStoreConfig('vendors/vendors_map/location_lat');
		return $location;
	}
	
	public function getDefaultLocationLng(){
		$location = Mage::getStoreConfig('vendors/vendors_map/location_lng');
		return $location;
	}

    public function getListAddressHtml(){
        $block = $this->getLayout()->createBlock("vendorsmap/shop_list")->setMap($this->getMapCollection());
        return $block->toHtml();
    }

    public function getRegionName($code){
        $region = Mage::getModel('directory/region')->load($code);
        return $region->getName();
    }

    public function getCountryName($code){
        $region = Mage::getModel('directory/country')->load($code);
        return $region->getName();
    }

    public function getScriptHtml(){
        $maps = $this->getMapCollection();
        $script = '<script type="text/javascript">';
        $script .=	'var neighborhoods = new Array();var messages = new Array();var infowindows = new Array();';
        $i = 0;
        foreach($maps as $map){
            $position = explode('|',$map->getData("position"));
            if($map->getData("region_id")){
                $region = $this->getRegionName($map->getData("region_id"));
            }
            else{
                if($map->getData("region")){
                    $region = $map->getData("region");
                }
                else{
                    $region = null;
                }
            }
            $data = array($map->getData("title"),$map->getData("address"),$map->getData("city"),$region,$this->getCountryName($map->getData("country_id")),$map->getData("telephone"),$map->getData("postcode"));
            $json_data = json_encode($data);
            $script .= 'neighborhoods['.$i.'] =  new google.maps.LatLng('.$position[0].','.$position[1].');';
            $script .= 'messages['.$i.'] = '.$json_data.';';
            $i++;
        }
        $script .= '</script>'	;
        return $script;
    }
}