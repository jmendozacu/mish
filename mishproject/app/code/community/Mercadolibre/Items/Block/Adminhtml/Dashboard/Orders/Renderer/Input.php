<?php
class Mercadolibre_Items_Block_Adminhtml_Itemorders_Renderer_Input extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
	   $html = "";
       switch($this->getColumn()->getId()){
            case "variation":
					$html = "";
					//get all items for the order
					$itemDetail = Mage::getModel('items/meliorderitems')->getCollection()
								-> addFieldToFilter('order_id',$row->getData('order_id'));
					$itemDetail -> getSelect()
								-> joinleft(array('mlitem'=>'mercadolibre_item'), "main_table.item_id = mlitem.meli_item_id", array('mlitem.permalink'))
								-> joinleft(array('mcm'=>'mercadolibre_categories_mapping'), "mlitem.category_id= mcm.mage_category_id", array('mcm.meli_category_id'));

					if(count($itemDetail->getData())>0){
						$html="";
						foreach($itemDetail->getData() as $rowItems){	
							
							$html .='<div style="margin-bottom:5px;">';
							if($rowItems['permalink']){
								$html .= '<a href="'.$rowItems['permalink'].'" target="_blank" >'.$rowItems['title'] .'</a><br />';
							} else {
								$html .= '<strong>'.$rowItems['title'] .'</strong><br />';
							}
							$html .='</div>';
							$html .= number_format($rowItems['unit_price'], 2, '.', '').' x '.$rowItems['quantity'].' unit <br />';
							//get items attributes
							$itemAttributeVarient = "";
							

							if($rowItems['variation_id']!=''){
							
								$itemAttributeVarient = Mage::getModel('items/meliordervariationattributes')->getCollection()
														-> addFieldToFilter('item_id',$rowItems['item_id'])
														-> addFieldToFilter('variation_id',$rowItems['variation_id'])
														-> addFieldToFilter('order_id',$row->getData('order_id'))
														-> addFieldToFilter('mca.category_id',$rowItems['meli_category_id']);
								$itemAttributeVarient   -> getSelect()
														-> joinleft(array('mca'=>'mercadolibre_category_attributes'), "main_table.attribute_id = mca.meli_attribute_id AND main_table.name = mca.meli_attribute_name", array('mca.attribute_id as auto_attribute_id','mca.category_id'))
														-> joinleft(array('mcav'=>'mercadolibre_category_attribute_values'), "mca.meli_attribute_id = mcav.attribute_id AND main_table.value_name = mcav.meli_value_name and  main_table.value_id = mcav.meli_value_id and mca.category_id = mcav.meli_category_id", array('mcav.meli_value_name_extended'))
														-> order( array('main_table.name Desc'));

								$itemAttributeVarientArr = array();
								$itemAttributeVarientArr = $itemAttributeVarient->getData();
							  	 if(count($itemAttributeVarientArr)>0){
									$firstColor = '';
									$secColor = '';
									foreach($itemAttributeVarientArr as $rowAttr){
										if($rowAttr['name']=="Color Secundario"){
											$secColor = '';
											$secColor = $rowAttr['meli_value_name_extended'];
										}elseif($rowAttr['name']=='Color Primario'){
											$firstColor = '';
											$firstColor = $rowAttr['meli_value_name_extended'];
										}else{
											$html .= '<div class="variation"><span class="varLabel">'.$rowAttr['name'].'</span><span class="varBox varSize">'.$rowAttr['value_name'].'</span>';
										}
								}
								$html .= '<div class="varBox color"><span class="varColor" title="Marrn" style="background-color:'.$secColor.';"></span><div class="maskColor-two"><span class="varColor color-two" title="Naranja" style="background-color:'.$firstColor.';"></span></div></div></div>';
								}
							}
						}
					}else{
						$html = 'No';
					}
					break;
				case "buyer_id":
					$html = "";
					$html .= '<div float:left; margin-bottom:5px;">';
					$html .= '<strong>'.ucwords($row->getData('buyer_Name')).'</strong><br/>';
					$html .= '<div style="color:#999999;">';
					$html .= $row->getData('buyer_email').'<br/>';
					$html .='</div>';
					if($row->getData('receiver_address') !=''){
						/*$receiver_address = unserialize($row->getData('receiver_address'));
						foreach($receiver_address as $rowAdd){
							if(isset($rowAdd['address_line']) && trim($rowAdd['address_line']) !='') $html .= $rowAdd['address_line']."<br />";
							if(isset($rowAdd['zip_code']) && trim($rowAdd['zip_code']) !='')$html .= $rowAdd['zip_code']."<br />";
							if(isset($rowAdd['city']['name']) && trim($rowAdd['city']['name']) !='')$html .= $rowAdd['city']['name']."<br />";
							if(isset($rowAdd['state']['name']) && trim($rowAdd['state']['name']) !='')$html .= $rowAdd['state']['name']."<br />";
							if(isset($rowAdd['country']['name']) && trim($rowAdd['country']['name']) !='')$html .= $rowAdd['country']['name'];
						}*/
					}
					$html .='</div>';
					break;
				case "shipping":
					$imgURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
					$html .= '<div>';
					switch($row->getData('shipment_type')){
						case "custom_shipping":
							$shipment_type = '<div title="Shipment with manual tracking." style="color:#999;">Custom Shipping</div>';
							break;
						case "shipping":
							$shipment_type = '<div title="Shipment with automatic tracking." style="color:#999;">Shipping</div>';
							break;
						default:
							$shipment_type ='<div title="Not Specify" style="color:#999;">Not Specify &nbsp;&nbsp;<img alt="Processing" height="16" width="23" src="'.$imgURL.'mercadolibre/unshipped.png" / ></div>&nbsp;&nbsp;';
					}
					$html .='<table width="100%" border="0" cellspacing="5" cellpadding="0" style="border:none!important;"><tr><td style="border:none!important;">Shipment Type:</td><td style="border:none!important;">'.$shipment_type.'</td></tr>';
					$html .='<tr>
								<td style="border:none!important;">Shipping Status:</td><td style="border:none!important;">';
				   if($row->getData('shipment_type') == 'custom_shipping'){

								/*----Shipping Status ---*/
					$statusShippingArray = array(
												''=>'Choose Action',
												'shipped'=>' Shipped &nbsp; ',
												);
					$html .= '<select name="shipping_status[]">';
							foreach($statusShippingArray as $keyS => $statusVal){
								$selStatus ='';
								if($row->getData('shipping_status') == $keyS) $selStatus = 'selected="selected"';
								$html .= '<option value="'.$keyS.'" '.$selStatus.'>'.$statusVal.'</option>';
							}
					$html .= '</select>&nbsp;&nbsp;';
					if($row->getData('shipping_status')=='delivered' || $row->getData('shipping_status')=='shipped'){
						$html .= '<img alt="Shipped" height="16" width="23" src="'.$imgURL.'mercadolibre/shipped.png" / >&nbsp;&nbsp;';
					}
					$html .='<tr><td style="border:none!important;">Shipped On:</td><td style="border:none!important;">';
					$html .= $row->getData('shipment_date_created');
					$html .='</td></tr>';
					$html .='<tr><td style="border:none!important;">Shipping Cost: </td><td style="border:none!important;">';
					$html .= $row->getData('shipping_cost');
					$html .='</td></tr>';
								  
					}else{
						$html .= $row->getData('shipping_status');
						$html .='</td>
								  </tr>';
					
						
					}
					$html .= '</table></div>';
					break;
				case "status":
					$html ='<table width="100%" border="0" cellspacing="5" cellpadding="0" style="border:none!important;">
							  <tr>
								<td style="border:none!important;">Payment Status:</td><td style="border:none!important;">';
								$imgURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
								if($row->getData('payment_status')=='paid'){
									$html .= '<div  style="color:#999;"><img height="16" width="23" src="'.$imgURL.'mercadolibre/paid.png" / >&nbsp;&nbsp;';
									$html .= ucfirst($row->getData('payment_status')).'<br/>';
									$html .= '</div>';
								}elseif($row->getData('payment_status')=='processing'){
									$html .= '<div style="color:#999;"><img height="16" width="23" src="'.$imgURL.'mercadolibre/processing.png" / >&nbsp;&nbsp;';
									$html .= ucfirst($row->getData('payment_status')).'<br/>';
									$html .= '</div>';
								}else{
									$html .= '<div style="color:#999;"><img height="16" width="23" src="'.$imgURL.'mercadolibre/unpaid.png" / >&nbsp;&nbsp;';
									$html .= ucfirst($row->getData('payment_status')).'<br/>';
									$html .= '</div>';
								}
								$html .='</td>
							  </tr>';
					$html .='<tr>
								<td style="border:none!important;">Order Status:</td><td style="border:none!important;5">';
					/*---Order Status---*/
					$statusOrderArray = array(
										''=>'Choose Action',
										'paid'=>' Paid &nbsp; ',
										/*'confirmed'=>'Confirmed',
										'payment_required'=>'Payment Required',
										'complete'=>'Completed',
										'cancelled'=>'Cancelled'*/
										);
					$html .='<input type="hidden" name="order_id[]" readonly="true" value="'.$row->getData('order_id').'">';
					$html .= '<select name="order_status[]">';
							foreach($statusOrderArray as $keyS => $statusVal){
								$selStatus ='';
								if($row->getData('status') == $keyS) $selStatus = 'selected="selected"';
								$html .= '<option value="'.$keyS.'" '.$selStatus.'>'.$statusVal.'</option>';
							}
					$html .= '</select>';
					$html .='</td>
							  </tr>';
					$html .='</table>';
					break;
				case "check_box":
			 			$checked = '';
						$html = '<input type="checkbox" id="checkbox_'.$row->getData('order_id').'" name="checkbox_'.$row->getData('order_id').'" '.$checked.'>';
					break;
				case "order_date":
						$html .= '<strong>Date Created:</strong><br /><div title="Shipment with manual tracking." style="color:#999;">'.$row->getData('date_created').'</div><br />';
						$html .= '<strong>Date Closed:</strong><br /><div title="Shipment with manual tracking." style="color:#999;">'.$row->getData('date_closed').'</div>';
						break;
				default:
						$html = 'No';
					 break;
        }
        return $html;
    }
}