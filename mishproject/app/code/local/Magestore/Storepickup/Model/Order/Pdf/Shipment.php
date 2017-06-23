<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Shipment PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Storepickup_Model_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment
{
    /**
     * Draw table header for product items
     *
     * @param  Zend_Pdf_Page $page
     * @return void
     */
     protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        // $this->_setFontRegular($page, 10);
        // $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        // $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        // $page->setLineWidth(0.5);
        // $page->drawRectangle(25, $this->y, 570, $this->y-15);
        // $this->y -= 10;
        // $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        // //columns headers
        // $lines[0][] = array(
        //     'text' => Mage::helper('sales')->__('Products'),
        //     'feed' => 100,
        // );

        // $lines[0][] = array(
        //     'text'  => Mage::helper('sales')->__('Qty'),
        //     'feed'  => 35
        // );

        // $lines[0][] = array(
        //     'text'  => Mage::helper('sales')->__('SKU'),
        //     'feed'  => 565,
        //     'align' => 'right'
        // );

        // $lineBlock = array(
        //     'lines'  => $lines,
        //     'height' => 10
        // );

        // $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        // $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        // $this->y -= 20;
    }
    
    protected function insertOrder(&$page, $obj, $putOrderId = true) {
       
                if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;

            $vendorid=$order['vendor_id'];
             $quantity= $order['total_qty_ordered'];
             $quantity = preg_replace('~\.0+$~','',$quantity);
              $weight=$order['weight'];
             $total=$order['subtotal'];
             $symbol=Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol(); 
            
            $collection=Mage::getModel('vendors/vendor')->load($vendorid);
            $vendoraddress=($collection['address'].","."<br>".$collection['city'].","."<br>".$collection['region'].",".$collection['country_id'].","."<br>".$collection['postcode']);
            $title= $collection->getTitle();
       
           $customername=$order['customer_firstname']." ".$order['customer_lastname'];
           
   
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
           $vendorid=$order['vendor_id'];
             $quantity= $order['total_qty_ordered'];
             $quantity = preg_replace('~\.0+$~','',$quantity);
              $weight=$order['weight'];
             $total=$order['subtotal'];
             $symbol=Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol(); 
             
            $collection=Mage::getModel('vendors/vendor')->load($vendorid);
            $vendoraddress=($collection['address'].","."<br>".$collection['city'].","."<br>".$collection['region'].",".$collection['country_id'].","."<br>".$collection['postcode']);
            $title= $collection->getTitle();
       
           $customername=$order['customer_firstname']." ".$order['customer_lastname'];
        }
        // echo $customer_firstname;
        // exit();
        
            $this->y = 800;
            $x = 200;
            $this->_setFontBold($page, 20);
            $page->drawText('Document to manifiesto', $x, $this->y, 'UTF-8');
            $this->_setFontBold($page, 13);
            $page->drawText('SELLER:-', 60, $this->y-25, 'UTF-8');
            $this->_setFontRegular($page, 13);
            $page->drawText(strip_tags(trim($title)),120, $this->y-25, 'UTF-8');
            $this->_setFontBold($page, 13);
            $page->drawText('DIRECTION SELLER:-', 60, $this->y-45, 'UTF-8');
            $this->_setFontRegular($page, 13);
            $page->drawText(strip_tags(trim($vendoraddress)),200, $this->y-45, 'UTF-8');
            $this->_setFontBold($page, 13);
            $page->drawText('CUSTOMER:-', 60, $this->y-65, 'UTF-8');
            $this->_setFontRegular($page, 13);
            $page->drawText(strip_tags(trim($customername)),135, $this->y-65, 'UTF-8');
           $this->_setFontBold($page, 13);
            $page->drawText('ADDRES TO DELIVERY CUSTOMER:-', 60, $this->y-85, 'UTF-8');
            $this->_setFontRegular($page, 13);
                 $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
            foreach ($shippingAddress as $key => $value) {
                if ($value !== '') {
                    $page->drawText($value, $x + 80, $this->y-85, 'UTF-8');
                    $this->y -= 10;
                }
                }
          $this->_setFontBold($page, 13);
            $page->drawText('BARCODE Order:-', 60, $this->y-125, 'UTF-8');
           $page->drawRectangle(60, $this->y - 135, $page->getWidth()-90, $this->y - 185, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
           $this->_setFontBold($page, 11);
        $page->drawText('Number of the Order', 80, $this->y - 145, 'UTF-8');
        $page->drawText('No of Units', 240, $this->y - 145, 'UTF-8');
        $page->drawText('Weight  Volumetric', 380, $this->y - 145, 'UTF-8');
        $page->drawLine(200, $this->y - 135 , 200, $this->y - 185);
        $page->drawLine(350, $this->y - 135, 350, $this->y - 185);
         $page->drawLine(60, $this->y - 150, $page->getWidth()-90, $this->y - 150);
         $this->_setFontRegular($page, 13);
       if ($putOrderId) {
            $page->drawText(
                Mage::helper('sales')->__('') . $order->getRealOrderId(), 83, $this->y - 170, 'UTF-8'
            );
        }
      $page->drawText(strip_tags(trim($quantity)),247, $this->y-170, 'UTF-8');
      $page->drawText(strip_tags(trim($weight)),387, $this->y-170, 'UTF-8');
      $page->drawRectangle(60, $this->y - 240, $page->getWidth()-90, $this->y - 440, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
      $this->_setFontBold($page, 12);
        $page->drawText('Total Cost Order in CLP', 70, $this->y - 255, 'UTF-8');
        // $page->drawRectangle(60, $this->y - 135, $page->getWidth()-90, $this->y - 185, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
         $this->_setFontRegular($page, 13);
          $page->drawText(strip_tags(trim($total)),90, $this->y-395, 'UTF-8');
         $page->drawText(strip_tags(trim($symbol)),83, $this->y-395, 'UTF-8');

      $this->_setFontBold($page, 12);
      $page->drawText('Sign  Delivery Provider',220,$this->y - 255, 'UTF-8');
      
        $text1='I declare that I receive from ';
        $text2='the seller the quantity of ';
        $text3='items/sku indicated in this ';
        $text4='manifesto related to the ';
        $text5='purchase order  indicated.';

        $this->_setFontRegular($page, 12);
        $page->drawText(strip_tags(trim($text1)),207, $this->y-275, 'UTF-8');
        $page->drawText(strip_tags(trim($text2)),207, $this->y-290, 'UTF-8');
        $page->drawText(strip_tags(trim($text3)),207, $this->y-305, 'UTF-8');
        $page->drawText(strip_tags(trim($text4)),207, $this->y-320, 'UTF-8');
        $page->drawText(strip_tags(trim($text5)),207, $this->y-335, 'UTF-8');
       
        $this->_setFontBold($page, 12);
        $page->drawText('Sign Seller ', 380, $this->y - 255, 'UTF-8');
         $text6='I declare that I deliver to ';
         $text7='supplier of the office quantity';
        $text8='of items/sku indicated in this';
         $text9='manifesto related to purchase';
          $text11='order indicated to be';
              $text12='delivered to client specified';
                $text13='in this manifesto.';
          $this->_setFontRegular($page, 12);
         $page->drawText(strip_tags(trim($text6)),362, $this->y-275, 'UTF-8');
          $page->drawText(strip_tags(trim($text7)),362, $this->y-290, 'UTF-8');
           $page->drawText(strip_tags(trim($text8)),362, $this->y-305, 'UTF-8');
            $page->drawText(strip_tags(trim($text9)),362, $this->y-320, 'UTF-8');
            $page->drawText(strip_tags(trim($text11)),362, $this->y-335, 'UTF-8');
             $page->drawText(strip_tags(trim($text12)),362, $this->y-350, 'UTF-8');
             $page->drawText(strip_tags(trim($text13)),362, $this->y-365, 'UTF-8');
            
        $page->drawLine(200, $this->y - 240 , 200, $this->y - 440);
        $page->drawLine(350, $this->y - 240, 350, $this->y - 440);
         $page->drawLine(60, $this->y - 380, $page->getWidth()-90, $this->y - 380);
         $this->_setFontRegular($page, 10);
         $page->drawText('Put Sign Here ', 220, $this->y - 435, 'UTF-8');
         $page->drawText('Put Sign Here ', 380, $this->y - 435, 'UTF-8');           
         $page->setFillColor(new Zend_Pdf_Color_Rgb(0.9312, 0.9212, 0.9212));




                 //       echo "123+++";
        //       exit();
        // if ($obj instanceof Mage_Sales_Model_Order) {
        //     $shipment = null;
        //     $order = $obj;
        // } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
        //     $shipment = $obj;
        //     $order = $shipment->getOrder();
        // }
         
        // if(!$order->getAwDeliverydateDate()) {              
        //       return parent::insertOrder($page, $obj, $putOrderId);
        // }

        // /* @var $order Mage_Sales_Model_Order */
        // $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));

        // $page->drawRectangle(25, 790, 570, 755);

        // $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        // $this->_setFontRegular($page);


        // if ($putOrderId) {
        //     $page->drawText(Mage::helper('sales')->__('Order # ') . $order->getRealOrderId(), 35, 770, 'UTF-8');
        // }
        // //$page->drawText(Mage::helper('sales')->__('Order Date: ') . date( 'D M j Y', strtotime( $order->getCreatedAt() ) ), 35, 760, 'UTF-8');
        // $page->drawText(Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false), 35, 760, 'UTF-8');

        // $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        // $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        // $page->setLineWidth(0.5);
        // $page->drawRectangle(25, 755, 275, 730);
        // $page->drawRectangle(275, 755, 570, 730);

        // /* Calculate blocks info */

        // /* Billing Address */
        // $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));

        // /* Payment */
        // $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
        //         ->setIsSecureMode(true)
        //         ->toPdf();
        // $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        // foreach ($payment as $key => $value) {
        //     if (strip_tags(trim($value)) == '') {
        //         unset($payment[$key]);
        //     }
        // }
        // reset($payment);

        // /* Shipping Address and Method */
        // if (!$order->getIsVirtual()) {
        //     /* Shipping Address */
        //     $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));

        //     $shippingMethod = $order->getShippingDescription();
        // }

        // $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        // $this->_setFontRegular($page);
        // $page->drawText(Mage::helper('sales')->__('SOLD TO:'), 35, 740, 'UTF-8');

        // if (!$order->getIsVirtual()) {
        //     $page->drawText(Mage::helper('sales')->__('SHIP TO:'), 285, 740, 'UTF-8');
        // } else {
        //     $page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, 740, 'UTF-8');
        // }

        // if (!$order->getIsVirtual()) {
        //     $y = 730 - (max(count($billingAddress), count($shippingAddress)) * 10 + 5);
        // } else {
        //     $y = 730 - (count($billingAddress) * 10 + 5);
        // }

        // $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        // $page->drawRectangle(25, 730, 570, $y);
        // $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        // $this->_setFontRegular($page);
        // $this->y = 720;

        // foreach ($billingAddress as $value) {
        //     if ($value !== '') {
        //         $page->drawText(strip_tags(ltrim($value)), 35, $this->y, 'UTF-8');
        //         $this->y -=10;
        //     }
        // }


        // if (!$order->getIsVirtual()) {
        //     $this->y = 720;
        //     foreach ($shippingAddress as $value) {
        //         if ($value !== '') {
        //             $page->drawText(strip_tags(ltrim($value)), 285, $this->y, 'UTF-8');
        //             $this->y -=10;
        //         }
        //     }

        //     $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        //     $page->setLineWidth(0.5);
        //     $page->drawRectangle(25, $this->y, 275, $this->y - 25);
        //     $page->drawRectangle(275, $this->y, 570, $this->y - 25);

        //     $this->y -=15;
        //     $this->_setFontBold($page);
        //     $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        //     $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
        //     $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y, 'UTF-8');

        //     $this->y -=10;
        //     $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

        //     $this->_setFontRegular($page);
        //     $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

        //     $paymentLeft = 35;
        //     $yPayments = $this->y - 15;
        // } else {
        //     $yPayments = 720;
        //     $paymentLeft = 285;
        // }

        // foreach ($payment as $value) {
        //     if (trim($value) !== '') {
        //         $page->drawText(strip_tags(trim($value)), $paymentLeft, $yPayments, 'UTF-8');
        //         $yPayments -=10;
        //     }
        // }

        // if (!$order->getIsVirtual()) {
        //     $this->y -=15;

        //     $page->drawText($shippingMethod, 285, $this->y, 'UTF-8');

        //     $yShipments = $this->y;

        //     $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " " . $order->formatPriceTxt($order->getShippingAmount()) . ")";

        //     $page->drawText($totalShippingChargesText, 285, $yShipments - 7, 'UTF-8');

        //     $yShipments -=10;
 
            
            
            
            
            
            
            
            
            
        //     /* Delivery date customization */
        //     Mage::helper('deliverydate/pdf')->insertDeliveryDate($page, $order, $yShipments);
        //     /* Delivery date customization */
            
            
           
            
            
            
            
            
            
            
            
          
        //     $yShipments -=10;

        //     $tracks = array();
        //     if ($shipment) {
        //         $tracks = $shipment->getAllTracks();
        //     }
        //     if (count($tracks)) {
                 //$page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        //         $page->setLineWidth(0.5);
        //         $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
        //         $page->drawLine(380, $yShipments, 380, $yShipments - 10);
        //         //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

        //         $this->_setFontRegular($page);
        //         $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        //         //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
        //         $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
        //         $page->drawText(Mage::helper('sales')->__('Number'), 385, $yShipments - 7, 'UTF-8');

        //         $yShipments -=17;
        //         $this->_setFontRegular($page, 6);
        //         foreach ($tracks as $track) {

        //             $CarrierCode = $track->getCarrierCode();
        //             if ($CarrierCode != 'custom') {
        //                 $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
        //                 $carrierTitle = $carrier->getConfigData('title');
        //             } else {
        //                 $carrierTitle = Mage::helper('sales')->__('Custom Value');
        //             }

        //             //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
        //             $maxTitleLen = 45;
        //             $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
        //             $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
        //             //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
        //             $page->drawText($truncatedTitle, 300, $yShipments, 'UTF-8');
        //             $page->drawText($track->getNumber(), 395, $yShipments, 'UTF-8');
        //             $yShipments -=7;
        //         }
        //     } else {
        //         $yShipments -= 7;
        //     }

        //     $currentY = min($yPayments, $yShipments);

        //     // replacement of Shipments-Payments rectangle block
        //     $page->drawLine(25, $this->y + 15, 25, $currentY);
        //     $page->drawLine(25, $currentY, 570, $currentY);
        //     $page->drawLine(570, $currentY, 570, $this->y + 15);

        //     $this->y = $currentY;
        //     $this->y -= 15;
        // }
    }
    
}
