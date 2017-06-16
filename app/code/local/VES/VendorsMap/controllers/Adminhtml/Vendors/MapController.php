<?php
include_once Mage::getBaseDir('code').'/local/VES/VendorsMap/Lib/Uploader.php';
class VES_VendorsMap_Adminhtml_Vendors_MapController extends Mage_Adminhtml_Controller_Action
{

	public function saveAction(){
        $vendor_id = $this->getRequest()->getParam('vendor_id');
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
                $map->setVendorId($vendor_id )->save();


                if($map->getId()){
                    if($data['logo']){
                        Mage::getModel('vendorsmap/map')->uploadFileAdmin($data['logo'],$map->getId(),$vendor_id);
                    }
                    $position = explode("|",$map->getData('position'));
                    $address = $this->getLayout()->createBlock('vendorsmap/adminhtml_vendor_map_list')->setTemplate('ves_vendorsmap/list.phtml')->setCurrentVendorId($vendor_id);

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
        $vendor_id = $this->getRequest()->getParam('vendor_id');
		$data = $this->getRequest()->getParam("map");
		$data['position'] = str_replace(",", "|", $data['position']);
		$id = $this->getRequest()->getParam("map_id");
		try{
            $data["attribute"] = implode(",",$data["attribute"]);
            if($data["region_id"]) $data["region"] = null;
            if($data["region"]) $data["region_id"] = null;
			$map = Mage::getModel('vendorsmap/map');
			$map->setData($data);
			/*An original unit is created will have status Expecting*/
			$map->setVendorId($vendor_id)->setId($id)->save();

			if($map->getId()){
                if($data['logo']){
                    Mage::getModel('vendorsmap/map')->uploadFileAdmin($data['logo'],$map->getId(),$vendor_id);
                }
				$position = explode("|",$map->getData('position'));
				$address = $this->getLayout()->createBlock('vendorsmap/adminhtml_vendor_map_list')->setTemplate('ves_vendorsmap/list.phtml')->setCurrentVendorId($vendor_id);
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
        $vendor_id = $this->getRequest()->getParam('vendor_id');
		$id = $this->getRequest()->getParam("id");

		try{
			$map = Mage::getModel('vendorsmap/map')->load($id);

            if($map->getLogo()){
                $path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."logo".DS."vendor".$vendor_id.DS."address".$map->getId().DS;
                unlink($path.base64_decode($map->getLogo()));
                rmdir($path);
            }

			$map->delete();
			$address = $this->getLayout()->createBlock('vendorsmap/adminhtml_vendor_map_list')->setTemplate('ves_vendorsmap/list.phtml')->setCurrentVendorId($vendor_id);
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
        $vendor_id = $this->getRequest()->getParam('vendor_id');
		$id = $this->getRequest()->getParam("id");
		$num = $this->getRequest()->getParam("num");
		try{
			$map = Mage::getModel('vendorsmap/map')->load($id);
			$position = explode("|",$map->getData('position'));
			$address = $this->getLayout()->createBlock('vendorsmap/adminhtml_vendor_map_form')->setTemplate('ves_vendorsmap/form.phtml')->setMarker($map)->setNum($num)->setCurrentVendorId($vendor_id);;
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
        $vendor_id = $this->getRequest()->getParam('vendor_id');

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
        $vendor_id = $this->getRequest()->getParam('vendor_id');
    	$image = $this->getRequest()->getParam('image');
    	$path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."tmp".DS."vendor".$vendor_id.DS.base64_decode($image);
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

        $vendor_id = $this->getRequest()->getParam('vendor_id');
        $id = $this->getRequest()->getParam('id');
        $map = Mage::getModel("vendorsmap/map")->load($id);
        $path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."logo".DS."vendor".$vendor_id.DS."address".$map->getId().DS.base64_decode($map->getLogo());
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
        $vendor_id = $this->getRequest()->getParam('vendor_id');
        $image = $this->getRequest()->getParam('image');
        $path =  Mage::getBaseDir('media').DS.'ves_vendors'.DS.'map'.DS."tmp".DS."vendor".$vendor_id;
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