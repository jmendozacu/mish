<?php

class VES_Commision_Model_Observer 
{

    public function productcatoryData($observer)
     {

         $invoice = $observer->getEvent()->getInvoice();

        /*  echo "<pre>++++Invoice";
          print_r($invoice->getData());
          echo "</pre>";*/

         $invoiceid = $invoice->getEntityId();
         $invoiceIncrementId = $invoice->getIncrementId();
         $order   = $invoice->getOrder();   
         $orderIncrementId = $order->getIncrementId();
         $vendorId = $order->getVendorId();
     
       $orderId = $order->getEntityId();
       $orders = Mage::getModel('sales/order')->load($orderId);  
       $order_item_collection = $orders->getItemsCollection(); 

       foreach ($order_item_collection as $item) {
          /*  echo "<pre>^^^^^Product";
            print_r($item->getData());
            echo "</pre>";*/
          
          $productid = $item->getProductId();
          $productQty = $item->getQtyOrdered();
          $productPrice = $item->getPrice();
          $productName = $item->getName();
          $productModel   = Mage::getModel('catalog/product')->load($productid);
      
                $category_ids = $productModel->getCategoryIds();
                    foreach ($category_ids as $cat) {
                        $categoryss = Mage::getModel('catalog/category')->load($cat);
                        $categoryName = $categoryss->getName();
                       /* echo "<pre>^^^^^categories";
                        print_r($categoryss->getData());
                        echo "</pre>";

                        exit;*/
          
                        $names = array();
                            foreach ($categoryss->getParentCategories() as $parent) {
                                $names[] = $parent->getID();
                            }
                                    $arrCount = count($names);
                                    $parentCategory =  $names[$arrCount-1];
                                    $parentCategoryModel = Mage::getModel('catalog/category')->load($parentCategory);
                              
                                          if($parentCategoryModel->getSetCommission()!=""){
                                        $commissionvalue= $parentCategoryModel->getSetCommission();

                                        $totalAmount = ($productPrice*$productQty);
                                        $amount = (($commissionvalue/100)*$totalAmount);
                                        $calculated_amount = ($totalAmount-$amount);

                                        $vendorCommissionModel = Mage::getModel('commision/vendorcommision')->load();
                                            $vendorCommissionModel->setInvoiceid($invoiceid);
                                            $vendorCommissionModel->setVendorId($vendorId);
                                            $vendorCommissionModel->setProductActualPrice($productPrice);
                                            $vendorCommissionModel->setInvoiceIncrementId($invoiceIncrementId);
                                            $vendorCommissionModel->setOrderid($orderId);
                                            $vendorCommissionModel->setOrderIncrementId($orderIncrementId);
                                            $vendorCommissionModel->setProductid($productid);
                                            $vendorCommissionModel->setProductName($productName);
                                            $vendorCommissionModel->setCategoryName($categoryName);
                                            $vendorCommissionModel->setProductQty($productQty);
                                            $vendorCommissionModel->setProductAmount($totalAmount);
                                            $vendorCommissionModel->setCalculatedCommission($calculated_amount);
                                            $vendorCommissionModel->setProCategorycommision($commissionvalue);
                                            $vendorCommissionModel->save();


                             }
                          }
                       } 
                      
                    }
                 }
    
    
