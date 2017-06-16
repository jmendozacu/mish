<?php
include_once Mage::getBaseDir('code').'/local/VES/VendorsMap/Lib/Uploader.php';
class VES_VendorsMap_Vendor_MapController extends VES_Vendors_Controller_Action
{
	public function saveAction(){
		$data = $this->getRequest()->getParams();
		$data['position'] = str_replace(",", "|", $data['position']);
        $map = Mage::getModel('vendorsmap/map')->getCollection()->addFieldToFilter("position",array("eq"=>$data['position']))->getFirstItem();
        if($map->getId()){
            $result = array('success'=>false,'msg'=>Mage::helper('vendorsmap')->__('A address with the same position already exists'));
        }
        else{
            try{
                $data["attribute"] = implode(",",$data["attribute"]);
                if($data["region_id"]) $data["region"] = null;
                if($data["region"]) $data["region_id"] = null;
                $map = Mage::getModel('vendorsmap/map');
                $map->setData($data);
                /*An original unit is created will have status Expecting*/
                $map->setVendorId($this->_getSession()->getVendorId())->save();
                if($map->getId()){
                    if($data['logo']){
                        Mage::getModel('vendorsmap/map')->upload($data['logo'],$map->getId());
                    }
                    $position = explode("|",$map->getData('position'));
                    $address = $this->getLayout()->createBlock('vendorsmap/map_list')->setTemplate('ves_vendorsmap/list.phtml')->setVendors($this->_getSession()->getVendorId());
                    $result = array(
                        'success'=>true,
                        "list"=>$address->toHtml(),
                        "position_lat"=>$position[0],
                        "position_lng"=>$position[1],
                        "address"=>$map->getData('address'),
                        "telephone"=>$map->getData('telephone'),
                        "title"=>$map->getData('title'),
                        "city"=>$map->getData('city'),
                        "country"=>Mage::helper("vendorsmap")->getCountryName($map->getData('country_id')),
                        "postcode"=>$map->getData('postcode'),
                        "logo"=>$map->getData('logo')
                    );
                    if($map->getData('region_id')){
                        $result["region"] = Mage::helper("vendorsmap")->getRegionName($map->getData('region_id'));
                    }
                    else{
                        $result["region"] = $map->getData('region');
                    }
                }else{
                    throw new Mage_Core_Exception(Mage::helper('vendorsmap')->__('Can not save the original box'));
                }
            }catch (Mage_Core_Exception $e){
                $result = array('success'=>false,'msg'=>$e->getMessage());
            }catch (Exception $e){
                $result = array('success'=>false,'msg'=>$e->getMessage());
            }
        }
		$this->getResponse()->setBody(json_encode($result));
	}
	public function saveformeditAction(){
		$data = $this->getRequest()->getParam("map");
		$data['position'] = str_replace(",", "|", $data['position']);
		$id = $this->getRequest()->getParam("map_id");
		try{
			$map = Mage::getModel('vendorsmap/map');
            $data["attribute"] = implode(",",$data["attribute"]);
            if($data["region_id"]) $data["region"] = null;
            if($data["region"]) $data["region_id"] = null;
			$map->setData($data);
			/*An original unit is created will have status Expecting*/
			$map->setVendorId($this->_getSession()->getVendorId())->setId($id)->save();

			if($map->getId()){
                if($data['logo']){
                    Mage::getModel('vendorsmap/map')->upload($data['logo'],$map->getId());
                }
				$position = explode("|",$map->getData('position'));
				$address = $this->getLayout()->createBlock('vendorsmap/map_list')->setTemplate('ves_vendorsmap/list.phtml')->setVendors($this->_getSession()->getVendorId());

                $result = array(
                    'success'=>true,
                    "list"=>$address->toHtml(),
                    "position_lat"=>$position[0],
                    "position_lng"=>$position[1],
                    "address"=>$map->getData('address'),
                    "telephone"=>$map->getData('telephone'),
                    "title"=>$map->getData('title'),
                    "city"=>$map->getData('city'),
                    "country"=>Mage::helper("vendorsmap")->getCountryName($map->getData('country_id')),
                    "postcode"=>$map->getData('postcode'),
                    "logo"=>$map->getData('logo')
                );
                if($map->getData('region_id')){
                    $result["region"] = Mage::helper("vendorsmap")->getRegionName($map->getData('region_id'));
                }
                else{
                    $result["region"] = $map->getData('region');
                }
			}else{
				throw new Mage_Core_Exception(Mage::helper('vendorsmap')->__('Can not save the original box'));
			}
		}catch (Mage_Core_Exception $e){
			$result = array('success'=>false,'msg'=>$e->getMessage());
		}catch (Exception $e){
			$result = array('success'=>false,'msg'=>$e->getMessage());
		}
		$this->getResponse()->setBody(json_encode($result));
	}

	public function deleteAction(){
		$id = $this->getRequest()->getParam("id");

		try{
			$map = Mage::getModel('vendorsmap/map')->load($id);

            if($map->getLogo()){
                $path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."logo".DS."vendor".$this->_getSession()->getVendorId().DS."address".$map->getId().DS;
                unlink($path.base64_decode($map->getLogo()));
                rmdir($path);
            }

			$map->delete();
			$address = $this->getLayout()->createBlock('vendorsmap/map_list')->setTemplate('ves_vendorsmap/list.phtml')->setVendors($this->_getSession()->getVendorId());
				//$outgoingBlock = $this->getLayout()->createBlock('parcelforwarding/parcel')->setTemplate('parcelforwarding/outgoing/list.phtml');
			$result = array('success'=>true,"list"=>$address->toHtml());
		}catch (Mage_Core_Exception $e){
			$result = array('success'=>false,'msg'=>$e->getMessage());
		}catch (Exception $e){
			$result = array('success'=>false,'msg'=>$e->getMessage());
		}
		$this->getResponse()->setBody(json_encode($result));
	}
	
	
	public function loadformAction(){
		$id = $this->getRequest()->getParam("id");
		$num = $this->getRequest()->getParam("num");
		try{
			$map = Mage::getModel('vendorsmap/map')->load($id);
			$position = explode("|",$map->getData('position'));
			$address = $this->getLayout()->createBlock('vendorsmap/map_form')->setTemplate('ves_vendorsmap/form.phtml')->setMarker($map)->setNum($num);
			//$outgoingBlock = $this->getLayout()->createBlock('parcelforwarding/parcel')->setTemplate('parcelforwarding/outgoing/list.phtml');
			$result = array('success'=>true,"html_form"=>$address->toHtml(),"position_lat"=>$position[0],"position_lng"=>$position[1]);
		}catch (Mage_Core_Exception $e){
			$result = array('success'=>false,'msg'=>$e->getMessage());
		}catch (Exception $e){
			$result = array('success'=>false,'msg'=>$e->getMessage());
		}
		$this->getResponse()->setBody(json_encode($result));
	}


    public function uploadAction(){
        $vendor_id = $this->getRequest()->getParam("vendor_id");
        try {
            $allowedExtensions = array('jpeg', 'jpg', 'gif', 'png');
            $sizeLimit = 10 * 1024 * 1024;
            $path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."tmp".DS."vendor".$vendor_id.DS;
            if(is_dir($path)==false){
                mkdir($path,0777,true);
            }
            $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
            $result = $uploader->handleUpload($path,$vendor_id);
            echo json_encode($result);
        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode());
        }
    }
    
    
    public function deleteLogoAjaxAction(){
    	$image = $this->getRequest()->getParam('image');
    	$path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."tmp".DS."vendor".$this->_getSession()->getVendorId().DS.base64_decode($image);
    	try {
    		unlink($path);
    		$result = array(
    				'errorcode' => false);
    	} catch (Exception $e) {
    		$result = array(
    				'error' => $e->getMessage(),
    				'errorcode' => true);
    	}
    	echo json_encode($result);
    }

    public function deleteLogoAddressAjaxAction(){
        $id = $this->getRequest()->getParam('id');
        $map = Mage::getModel("vendorsmap/map")->load($id);
        $path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."logo".DS."vendor".$this->_getSession()->getVendorId().DS."address".$map->getId().DS.base64_decode($map->getLogo());
        $map->setData("logo",NULL);
        try {
            $map->save();
            unlink($path);
            $result = array(
                'errorcode' => false);
        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => true);
        }
        echo json_encode($result);

    }


    public function clearImageTmpAjaxAction(){

        $image = $this->getRequest()->getParam('image');
        $path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."tmp".DS."vendor".$this->_getSession()->getVendorId();
        try {
            Mage::helper("vendorsmap")->rrmdir($path);
            $result = array(
                'errorcode' => false);
        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => true);
        }
        echo json_encode($result);
    }
}