<?php

class Mercadolibre_Items_Block_Adminhtml_Itempublishing_Renderer_Hidden extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract


{
    public function render(Varien_Object $row)
    {
		$qtyDisplay ="";
		$html = '';	
		$priceDisplay = '';	
		switch($this->getColumn()->getId()){
			case "meli_quantity":		
				if($row->getData('start_time')!= ""){
					$html .= '<div  style="width:125px; float:left; margin-bottom:5px; font-size:11px"><strong>Start Time:</strong> <br />'.$row->getData('start_time').' </div>';
				}
				if($row->getData('stop_time')!= ""){
					/* Stop_time */	
					if($row->getData('stop_time') < now()){
					$html .= '<div  style="width:125px; float:left; margin-bottom:5px;font-size:11px; color:red"><strong>Stop Time:</strong> <br />'. $row->getData('stop_time').' </div>';
					}else{
					$html .= '<div  style="width:125px; float:left; margin-bottom:5px;font-size:11px"><strong>Stop Time:</strong> <br />'. $row->getData('stop_time').' </div>';
					}
				}
				if($row->getData('end_time')!= ""){	/* end_time */	
					$html .= '<div  style="width:125px; float:left; margin-bottom:5px;font-size:11px"><strong>End Time:</strong> <br />'. $row->getData('end_time').' </div>';
				}
				break;

			case "descriptions":			     
				$html .= $row->getData('description_header');
				if(trim($row->getData('main_image'))!=''){
					$html .= '<img src="'.$row->getData('main_image').'" alt="Main Image" width="100" />';
				}
				$html .= $row->getData('description_body');
				$html .= $row->getData('description_footer');
			break;

			case "meli_category_name":			
				$commonModel = Mage::getModel('items/common');
				$str = $commonModel->getMeliCategoryNameByMageCatId(array($row->getData('category_id')));
				$html .= $str;
			break;
			case "meli_category_id":			
			   $cache = Mage::app()->getCache();
			   if(!$cache->load('meliCatCollection_data')){
			   		 $meliCatCollection = Mage::getModel('items/melicategories')
									   -> getCollection()
									   -> addFieldToSelect('meli_category_id')
									   -> addFieldToSelect('meli_category_name');
					  $meliCatCollection-> getSelect()
									    -> joinleft(array('melicatfilter'=>'mercadolibre_categories_filter'), "main_table.meli_category_id = melicatfilter.meli_category_id AND melicatfilter.store_id = '".Mage::helper('items')-> _getStore()->getId()."' ",array('melicatfilter.meli_category_path'));
					 $meliCatCollectionArr = $meliCatCollection -> getData();		   
			  		 $cache->save(serialize($meliCatCollectionArr), "meliCatCollection_data", array("meli_Cat_Collection_data"), 60*60);
			   } else {
			   		$meliCatCollectionArr = unserialize($cache->load('meliCatCollection_data'));
			   }
				$mlCatArr = array();
				foreach($meliCatCollectionArr as $rowMLCat){
					$mlCatArr[$rowMLCat['meli_category_id']] = $rowMLCat['meli_category_name'];
					$mlCatPathArr[$rowMLCat['meli_category_id']] = $rowMLCat['meli_category_path'];
				} 
				$html = '<a href="javascript:void(0);" title="'.$mlCatPathArr[$row->getData($this->getColumn()->getIndex())].'" >'.$mlCatArr[$row->getData($this->getColumn()->getIndex())].'</a>';
			break;
			case "meli_item_id":
				$publishStatus = '';
				$modifyList = '';
				switch($row->getData('status')){
					case "not_yet_active":
						$publishStatus = '<div title="The item is waiting for activation."><strong>Status:</strong> Not Yet Active</div>';
					break;
					case "payment_required":
						$publishStatus = '<div title="Requires the seller pays the listing fee before activation."><strong>Status:</strong> Payment Required</div>';
					break;
					case "paused":
						$publishStatus = '<div title="The item was paused by the seller or other reason."><strong>Status:</strong> Paused</div>';
						$modifyList = '<tr><td align="center"><input class="form-button scalable save" onclick="setLocation(\''.$this->getUrl('items/adminhtml_itempublishing/putStatus/action/active/id/'.$row->getData('product_id')).'\');"  type="button" name="relist" value="Reactivate"></td></tr>';
						
						//$modifyList .= '<tr><td align="center"><input class="form-button scalable save" onclick="setLocation(\''.$this->getUrl('items/adminhtml_itempublishing/putStatus/action/closed/id/'.$row->getData('product_id')).'\');"  type="button" name="relist" value="Close"></td></tr>';
						
					break;
					case "active":
						$publishStatus = '<div title="Active"><strong>Status:</strong> Active</div>';
						if(trim($row->getData('sub_status')) != 'imported'){
						$modifyList = '<tr><td align="center"><input class="form-button scalable save" onclick="setLocation(\''.$this->getUrl('items/adminhtml_itempublishing/putStatus/action/paused/id/'.$row->getData('product_id')).'\');"  type="button" name="revise_modify" value="Pause"></td></tr>';

						$modifyList .= '<tr><td align="center"><input class="form-button scalable save" onclick="setLocation(\''.$this->getUrl('items/adminhtml_itempublishing/index/revise/'.$row->getData('product_id')).'\');"  type="button" name="revise_modify" value="Revise"></td></tr>';
						}
						
						break;
					case "closed":
						$publishStatus = '<div title="Closed"><strong>Status:</strong> Closed</div>';
						$modifyList = '<tr><td align="center"><input class="form-button scalable save" onclick="setLocation(\''.$this->getUrl('items/adminhtml_itempublishing/relist/id/'.$row->getData('product_id')).'\');"  type="button" name="relist" value="Relist"></td></tr>';
						break;
					case "under_review":
						$publishStatus = '<div title="The item is being reviewed by MercadoLibre">Under Review</div>';
						break;
					case "inactive":
						$publishStatus = '<div title="Inactive"><strong>Status:</strong> Inactive</div>';
						break;
					default:
						$publishStatus = '';
						break;
				}
				$html .='<table width="100%" border="0" cellspacing="0" cellpadding="0">
						  <tr><td align="center"><strong>';
						  	if(trim($row->getData('sub_status')) == 'imported'){
						  		$html .= ucfirst($row->getData('sub_status'));
							}
							$html .='</strong>'.$publishStatus.'</td></tr>';
						  if($row->getData('permalink')){	
						 	 $html .='<tr><td align="center"><a href="'.$row->getData('permalink').'" title="View Item" target="_blank"><strong>'.$row->getData('meli_item_id').'</strong></a></td></tr>';
						  } else {
						  	$html .='<tr><td align="center"><strong>'.$row->getData('meli_item_id').'</strong></td></tr>';
						  }
	  
				$html .= '<tr><td align="center"><input type="hidden" value="'.$row->getData('product_id').'" name="product_id[]">';
				if($row->getData('sent_to_publish') == 'Published' && $row->getData('status') == 'active'){
					$html .= '<div  style="width:70px; margin-bottom:5px; background-color:#EFF5EA; color:#3D6611 ; border:1px solid #95A486;  padding:2px 5px 2px 0px ; margin-top:5px;">'. $row->getData('sent_to_publish').' </div>';
				} else if($row->getData('sent_to_publish') == 'Published' && $row->getData('status') != 'active'){
					$html .= '<div  style="width:70px; margin-bottom:5px; background-color:#EFF5EA; color:#3D6611 ; border:1px solid #95A486;  padding:2px 5px 2px 0px ; margin-top:5px;">Posted</div>';
				} else {
					$html .= '<div  style="width:70px; margin-bottom:5px; background-color:#FAEBE7; color:#DF280A ; border:1px solid #F16048;  padding:2px 5px 2px 5px ; margin-top:5px;">'. $row->getData('sent_to_publish').' </div>';
				}
				$html .= '</td>
						  </tr>';
				//if($row->getData('status') == 'active'){
				$html .= $modifyList;
				//}
				$html .='</table>';
				break; 
			case "variation":
					$html = '';
		   			if($row->getData('has_attributes')){
						$revise = '';
						if($this->getRequest()->getParam('revise')){
							$revise = $this->getRequest()->getParam('revise');	
						}				
						$html = '<div id="variation_'.$row->getData('product_id').'"><a href="javascript:void(0);" title="Click here to view variation " onclick = showItemVariationOnPublish(\'variation_'.$row->getData('product_id').'\',\''.$row->getData('product_id').'\',\''.$revise.'\'); >View Variation</a></div>';
						$html .= '<div style="display:none" id="hide_variation_'.$row->getData('product_id').'"><a href="javascript:void(0);" title="Click here to hide variation " onclick = hideItemVariationOnPublish(\'variation_'.$row->getData('product_id').'\',\''.$row->getData('product_id').'\',\''.$revise.'\'); >Hide Variation</a></div>';
						$html .= '<div id="data_variation_'.$row->getData('product_id').'"></div>';	
					} else {
						$html = 'No Variation';
					}
					break;
			 case "check_box":
					$checked = '';
					$html = '';
					$revise = '';
					
/*					$checked = '';
					if($this->getRequest()->getParam('revise')){
						$checked = 'checked="checked"';
					}
					$html = '<input type="checkbox" id="checkbox_'.$row->getData('product_id').'" name="checkbox_'.$row->getData('product_id').'" '.$checked.'>';*/
					
					if($this->getRequest()->getParam('revise')){
						$checked = 'checked="checked"';
						$revise = $this->getRequest()->getParam('revise');
					}
					$expandvariation = 0;
		   			if($row->getData('has_attributes')){ 
						if(Mage::getStoreConfig("mlitems/globalattributesmapping/onitemspublishexpandvariation",Mage::app()->getStore())){
							$expandvariation = Mage::getStoreConfig("mlitems/globalattributesmapping/onitemspublishexpandvariation",Mage::app()->getStore());
						}
						if($expandvariation){										
							$html = '<input type="checkbox" id="checkbox_'.$row->getData('product_id').'" name="checkbox_'.$row->getData('product_id').'" onclick = showItemVariationOnPublish(\'variation_'.$row->getData('product_id').'\',\''.$row->getData('product_id').'\',\''.$revise.'\'); >';
						} else {
							$html = '<input type="checkbox" id="checkbox_'.$row->getData('product_id').'" name="checkbox_'.$row->getData('product_id').'" >';
						}
					} else if($revise) {
						$html = '<input type="checkbox" id="checkbox_'.$row->getData('product_id').'" name="checkbox_'.$row->getData('product_id').'">';
					}
					break;
			case "profiles":
					if($row->getData('template_name')!= ""){
						$html = '<div  style="width:125px; float:left; margin-bottom:5px; font-size:11px"><strong>Listing Type:</strong> <br />'.$row->getData('template_name').' </div>';
					}
					if($row->getData('shipping_profile')!= ""){
						$html .= '<div  style="width:125px; float:left; margin-bottom:5px;font-size:11px"><strong>Shipping:</strong> <br />'. $row->getData('shipping_profile').' </div>';
					}
					if($row->getData('payment_method_id')!= ""){
						$paymentMethodIdSel = array();
						$paymentMethodIdSel = explode(',',$row->getData('payment_method_id'));
						$meliPaymentDetailCollection = Mage::getModel('items/melipaymentmethods')
													->getCollection()
													->addFieldToFilter('main_table.id', array('in' => $paymentMethodIdSel))
													->addFieldToSelect('payment_name');
						if(count($meliPaymentDetailCollection->getData()) > 0){
							foreach($meliPaymentDetailCollection->getData() as $rowPay){
								$paymentMethodIdArr[] = $rowPay['payment_name'];
							}
							$paymentMethods = implode(' , ', $paymentMethodIdArr);
						}
					}else {
						$paymentMethods = 'Not Specified';
					}
					$html .= '<div  style="width:125px; float:left; margin-bottom:5px;font-size:11px"><strong>Payment Method:</strong> <br />'. $paymentMethods.' </div>';
					break;			
			default:
				$html = $row->getData($this->getColumn()->getIndex());
			break;
		}
		return $html;
    }
}