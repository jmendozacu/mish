<?php
class Mercadolibre_Items_Block_Adminhtml_Itemorders_Renderer_Input extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
	   $html = "";
       switch($this->getColumn()->getId()){
	   		case "order_id":
				$mlFeedback = Mage::getModel('items/melifeedbacks')->getCollection()
							-> addFieldToFilter('order_id',$row->getData('order_id'))
							-> addFieldToFilter('reply',array('neq' => ''));
		   		$html = $row->getData('order_id');
				$html .= '<input type="hidden" value="'.$row->getData('order_id').'" name="meli_orderid"></input>';
				if($mlFeedback->count()>0){
					$html .= '<br /><a href="'.$this->getUrl('items/adminhtml_feedbacks', array('order' => $row->getData('order_id'))).'" title="View Feedback" >View Feedback</a>';
				}else{
					$html .= '<br /><a href="'.$this->getUrl('items/adminhtml_feedbacks/new', array('orderid' => $row->getData('order_id'))).'" title="Give Feedback" >Give Feedback</a>';

				}
		  		break;
            case "variation":
					$html = "";
					//get all items for the order
					$itemDetail = Mage::getModel('items/meliorderitems')->getCollection()
								-> addFieldToFilter('main_table.order_id',$row->getData('order_id'))
								-> addFieldToFilter('main_table.item_id',$row->getData('item_id'))
								-> addFieldToFilter('mcm.store_id',Mage::helper('items')->_getStore()->getId());
					$itemDetail -> getSelect()
								-> joinleft(array('mlitem'=>'mercadolibre_item'), "main_table.item_id = mlitem.meli_item_id", array('mlitem.permalink'))
								-> joinleft(array('mcm'=>'mercadolibre_categories_mapping'), "mlitem.category_id= mcm.mage_category_id", array('mcm.meli_category_id'));

					if(count($itemDetail->getData())>0){
						$html="";
						foreach($itemDetail->getData() as $rowItems){	
							
							$html .='<div style="margin-bottom:5px;">';
							if(isset($rowItems['permalink']) && trim($rowItems['permalink'])!=''){
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
				case "email":
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
				case "check_box":
			 			$checked = '';
						$html = '<input type="checkbox" id="checkbox_'.$row->getData('order_id').'" name="checkbox_'.$row->getData('order_id').'" '.$checked.'>';
					break;
				case "order_date":
						$html .= '<strong>Date Created:</strong><br /><div title="Order Date" style="color:#999;">'.$row->getData('date_created').'</div><br />';
						$html .= '<strong>Date Closed:</strong><br /><div title="Order Date" style="color:#999;">'.$row->getData('date_closed').'</div>';
						break;
				/*case "sale_date":
						$html .= '<div title="Sale Date">'.$row->getData('date_created').'</div><br />';
						break; */
				case "mage_order_Id":
						if($row->getData('mage_order_Id')){
							$html .= '<div title="Magento Order ID" ><a target="_BLANK" href="'.Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getData('mage_order_Id'))).'">'.$row->getData('mage_order_number').'</a></div><br />';
						}else{
							$html .= '<div title="Magento Order No"> N/A </div><br />';
						}
						break;
				default:
						$html = 'No';
					 break;
        }
        return $html;
    }
}