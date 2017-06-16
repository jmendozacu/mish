<?php

class Ves_Tempcp_MinicartController extends Mage_Core_Controller_Front_Action {

    public function indexAction(){
    	$this->loadLayout();

    	if(($minicart_head = $this->getLayout()->getBlock("minicart_head")) && !Mage::registry('minicart_head')) {
            Mage::register('minicart_head', $minicart_head );
        }
    	$json = array();
        $json['html'] =  Mage::helper("ves_tempcp/framework")->getMinicartHtml();
        $json['summary_qty'] = Mage::getSingleton('checkout/cart')->getSummaryQty();
        $json['summary_qty'] = !empty($json['summary_qty'])?$json['summary_qty']:0;
        $json['subtotal'] = Mage::helper('ves_tempcp')->getCartSubtotal();
        
        echo Mage::helper('core')->jsonEncode( $json );

    }
}

?>
