<?php
class EmizenTech_CatAttribute_Adminhtml_CatattributeController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Backend Page Title"));
	   $this->renderLayout();
    }

    public function vandorID()
    {
    	$proxy = new SoapClient('https://mgtoempresastagingucv-e-servicenet.stratus5.net/index.php/api/soap/?wsdl');
		$sessionId = $proxy->login('test', '123456');
		// $vendorListFilter = array(array('vendor_id'=>'navneet_sharma'));
		$vendorListResult = $proxy->call($sessionId, 'vendor.list');
		
		$vendorID = array();
		foreach ($vendorListResult as $value)
		{
			$vendorID[] = $value['id'];
		}
		return $vendorID;
    }

    public function vandorProducts()
    {
    	$proxy = new SoapClient('https://mgtoempresastagingucv-e-servicenet.stratus5.net/index.php/api/soap/?wsdl');
	    $sessionId = $proxy->login('test', '123456');
	    $productArray = array();

    	foreach ($this->vandorID() as $vnID)
    	{
    		$vendorProductFilter = array(array('vendor_id'=>$vnID));
			$vendorProductList = $proxy->call($sessionId, 'catalog_product.list',$vendorProductFilter);
			
			foreach ($vendorProductList as $value)
			{
				$media = $proxy->call($sessionId, 'catalog_product_attribute_media.list', $value['product_id']);
				$mediaImg = array();
				foreach ($media as $img)
				{
					$mediaImg[] = $img;
				}
				$stocks = $proxy->call($sessionId, 'cataloginventory_stock_item.list', $value['product_id']);
				$vendorProductInfo = $proxy->call($sessionId, 'catalog_product.info',$value['product_id']);
				$vendorProductInfo['gellery'] = $mediaImg;
				$vendorProductInfo['is_in_stock'] = $stocks[0]['is_in_stock'];
				$vendorProductInfo['qty'] = $stocks[0]['qty'];
				// echo "<pre>"; print_r($vendorProductInfo); die;
				$productArray[] = $vendorProductInfo;
			}
    	}
    	
		return $productArray;
    }

    public function syncAction()
    {
    	die('ssss');
    	$client = new SoapClient('http://letratec.nextmp.net/letratec/index.php/api/soap/?wsdl');
		$sessId = $client->login('test1', '123456');
    	foreach ($this->vandorProducts() as $product)
    	{
		    $pro_id = Mage::getModel('catalog/product')->getIdBySku(trim($product['sku']));
		    if($pro_id)
		    {

		    	$client->call($sessId, 'catalog_product.update', 
											array($product['sku'],
													array(
													    'categories' => $product['categories'],
													    'websites' => $product['websites'],
													    'name' => $product['name'],
													    'description' => $product['description'],
													    'short_description' => $product['short_description'],
													    'weight' => $product['weight'],
													    'status' => $product['status'],
													    'url_key' => $product['url_key'],
													    'url_path' => $product['url_path'],
													    'visibility' => $product['visibility'],
													    'price' => $product['price'],
													    'tax_class_id' => $product['tax_class_id'],
													    'meta_title' => $product['meta_title'],
													    'meta_keyword' => $product['meta_keyword'],
													    'meta_description' => $product['meta_description'],
													    'stock_data' => array(
											                'qty' => $product['qty'], 
											                'is_in_stock' => $product['is_in_stock'],
											                'manage_stock ' => 1
											            )
													)
												)
				);
				
				$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
                $items = $mediaApi->items($pro_id);
                foreach($items as $item)
                {
                    $mediaApi->remove($pro_id, $item['file']);
                }

				foreach ($product['gellery'] as $gell)
				{
					$image_data = array(
									    'file' => array(
									        'content' => base64_encode(file_get_contents($gell['url'])),
											'mime' => 'image/jpeg'
									    ),
									    'label'    => $gell['label'],
									    'position' => $gell['position'],
									    'types'    => $gell['types'],
									    'exclude'  => $gell['exclude']
									);

					$resultss = $client->call($sessId, 'product_media.create', array($pro_id, $image_data));
					//echo "<pre>"; print_r($resultss);
			    }

		    }
		    else
		    {
		    	$resultzs = $client->call($sessId, 'catalog_product.create', 
											array($product['type'], $product['set'], $product['sku'],
													array(
													    'categories' => $product['categories'],
													    'websites' => $product['websites'],
													    'name' => $product['name'],
													    'description' => $product['description'],
													    'short_description' => $product['short_description'],
													    'weight' => $product['weight'],
													    'status' => $product['status'],
													    'url_key' => $product['url_key'],
													    'url_path' => $product['url_path'],
													    'visibility' => $product['visibility'],
													    'price' => $product['price'],
													    'tax_class_id' => $product['tax_class_id'],
													    'meta_title' => $product['meta_title'],
													    'meta_keyword' => $product['meta_keyword'],
													    'meta_description' => $product['meta_description'],
													    'stock_data' => array(
											                'qty' => $product['qty'], 
											                'is_in_stock' => $product['is_in_stock'],
											                'manage_stock ' => 1
											            )
													)
												)
				);

				foreach ($product['gellery'] as $gell)
				{
					$image_data = array(
									    'file' => array(
									        'content' => base64_encode(file_get_contents($gell['url'])),
											'mime' => 'image/jpeg'
									    ),
									    'label'    => $gell['label'],
									    'position' => $gell['position'],
									    'types'    => $gell['types'],
									    'exclude'  => $gell['exclude']
									);

					$client->call($sessId, 'product_media.create', array($resultzs, $image_data));
					//echo "<pre>"; print_r($resultss);
			    }
		    }

		}

		Mage::getSingleton('core/session')->addSuccess('Product synchronization has been run successfully.');
		
    	$this->_redirectReferer();
    }

    public function catAction()
    {
    	if (!$link = mysql_connect('localhost', 'root', '1q2w3e')) {
		    echo 'Could not connect to mysql';
		    exit;
		}

		if (!mysql_select_db('EmizenTech6', $link)) {
		    echo 'Could not select database';
		    exit;
		}

		$sql    = 'SELECT * FROM ves_vendor_catalog_category WHERE vendor_id = 2';
		$result = mysql_query($sql, $link);

		if (!$result) {
		    echo "DB Error, could not query the database\n";
		    echo 'MySQL Error: ' . mysql_error();
		    exit;
		}

		while ($row = mysql_fetch_assoc($result))
		{
		    $explodeRowPth = explode("/",$row['path']);
		    $tArr = '';
		    foreach ($explodeRowPth as $value)
		    {
		    	$tArr .= $this->catName($value)."/";
		    }
		    $catPath = substr($tArr,0,strlen($tArr)-1);
		    $this->_addCategories($catPath,Mage::app()->getStore());
		}
		Mage::getSingleton('core/session')->addSuccess('Category synchronization has been run successfully.');
		
    	$this->_redirectReferer();
    }

    public function catName($id)
    {
    	if (!$link = mysql_connect('localhost', 'root', '1q2w3e')) {
		    echo 'Could not connect to mysql';
		    exit;
		}

		if (!mysql_select_db('EmizenTech6', $link)) {
		    echo 'Could not select database';
		    exit;
		}

		$sql    = 'SELECT * FROM ves_vendor_catalog_category WHERE category_id = '.$id;
		$result = mysql_query($sql, $link);

		if (!$result) {
		    echo "DB Error, could not query the database\n";
		    echo 'MySQL Error: ' . mysql_error();
		    exit;
		}
		$r = array();
		$row = mysql_fetch_assoc($result);
		return $row['name'];
		
    }

    public function orderAction()
    {
    	$vendorOrderFilter = array(array('vendor_id'=>$this->vandorID()));
    	$proxy = new SoapClient('http://devet.emizentech.com/serjio/index.php/api/soap/?wsdl');
    	$sessionId = $proxy->login('test', 'test@123');
		// $vendorOrderList = $proxy->call($sessionId, 'order.list',$vendorOrderFilter);
		$vendorOrderList = $proxy->call($sessionId, 'sales_order.info','100000001');

		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();
		// Start New Sales Order Quote
		$quote = Mage::getModel('sales/quote')->setStoreId($store->getId());
		 
		// Set Sales Order Quote Currency
		// $quote->setCurrency($order->AdjustmentAmount->currencyID);
		
		// Configure Notification
		$quote->setSendCconfirmation(1);
		// echo "<pre>"; print_r($vendorOrderList); die;
		foreach ($vendorOrderList['items'] as $value)
		{
			$pro_id = Mage::getModel('catalog/product')->getIdBySku(trim($value['sku']));
			$product = Mage::getModel('catalog/product')->load($pro_id);
		    $quote->addProduct($product,new Varien_Object(array('qty'   => 1)));
		}

		// Set Sales Order Billing Address
		$billingAddress = $quote->getBillingAddress()->addData(array(
							 'customer_address_id' => $vendorOrderList['billing_address']['customer_address_id'],
							 'prefix' => $vendorOrderList['billing_address']['prefix'],
							 'firstname' => $vendorOrderList['billing_address']['firstname'],
							 'middlename' => $vendorOrderList['billing_address']['middlename'],
							 'lastname' =>$vendorOrderList['billing_address']['lastname'],
							 'suffix' => $vendorOrderList['billing_address']['suffix'],
							 'company' =>$vendorOrderList['billing_address']['company'], 
							 'street' => $vendorOrderList['billing_address']['street'],
							 'city' => $vendorOrderList['billing_address']['city'],
							 'country_id' => $vendorOrderList['billing_address']['country_id'],
							 'region' => $vendorOrderList['billing_address']['region_id'],
							 'postcode' => $vendorOrderList['billing_address']['postcode'],
							 'telephone' => $vendorOrderList['billing_address']['telephone'],
							 'fax' => $vendorOrderList['billing_address']['fax'],
							 'vat_id' => $vendorOrderList['billing_address']['vat_id'],
							 'save_in_address_book' => $vendorOrderList['billing_address']['address_id']
							));
		// Set Sales Order Shipping Address
		$shippingAddress = $quote->getShippingAddress()->addData(array(
						     'customer_address_id' => $vendorOrderList['shipping_address']['customer_address_id'],
							 'prefix' => $vendorOrderList['shipping_address']['prefix'],
							 'firstname' => $vendorOrderList['shipping_address']['firstname'],
							 'middlename' => $vendorOrderList['shipping_address']['middlename'],
							 'lastname' =>$vendorOrderList['shipping_address']['lastname'],
							 'suffix' => $vendorOrderList['shipping_address']['suffix'],
							 'company' =>$vendorOrderList['shipping_address']['company'], 
							 'street' => $vendorOrderList['shipping_address']['street'],
							 'city' => $vendorOrderList['shipping_address']['city'],
							 'country_id' => $vendorOrderList['shipping_address']['country_id'],
							 'region' => $vendorOrderList['shipping_address']['region_id'],
							 'postcode' => $vendorOrderList['shipping_address']['postcode'],
							 'telephone' => $vendorOrderList['shipping_address']['telephone'],
							 'fax' => $vendorOrderList['shipping_address']['fax'],
							 'vat_id' => $vendorOrderList['shipping_address']['vat_id'],
							 'save_in_address_book' => $vendorOrderList['shipping_address']['address_id']
		 				));

		// Collect Rates and Set Shipping & Payment Method
 		$shippingAddress->setCollectShippingRates(true)
                 ->collectShippingRates()
                 //->setShippingMethod($vendorOrderList['shipping_method'])
                 ->setShippingMethod('flatrate_flatrate')
                 ->setPaymentMethod($vendorOrderList['payment']['method']);

  //       foreach ($quote->getShippingAddress()->getShippingRatesCollection() as $key => $value)
		// {
  //       	echo "<pre>"; print_r($value->getData()); die;
  //       }

        // Set Sales Order Payment
		 $quote->getPayment()->importData(array('method' => $vendorOrderList['payment']['method']));
		 
		 // Collect Totals & Save Quote
		 $quote->collectTotals()->save();
		 
		 // Create Order From Quote
		 $service = Mage::getModel('sales/service_quote', $quote);
		 $service->submitAll();
		 $increment_id = $service->getOrder()->getRealOrderId();
		 
		 // Resource Clean-Up
		 $quote = $customer = $service = null;
		 
		 // Finished
		 echo $increment_id; die;

		// echo "<pre>"; print_r($vendorOrderList);
    }

    public function _addCategories($categories, $store)
    {
        $rootId = 2;
        if (!$rootId)
        {
            return array();
        }
        $rootPath = '1/'.$rootId;
        if (empty($this->_categoryCache[$store->getId()]))
        {
            $collection = Mage::getModel('catalog/category')->getCollection()
                ->setStore($store)
                ->addAttributeToSelect('name');
            $collection->getSelect()->where("path like '".$rootPath."/%'");

            foreach ($collection as $cat)
            {
                $pathArr = explode('/', $cat->getPath());
                $namePath = '';
                for ($i=2, $l=sizeof($pathArr); $i<$l; $i++)
                {
                    $name = $collection->getItemById($pathArr[$i])->getName();
                    $namePath .= (empty($namePath) ? '' : '/').trim($name);
                }
                $cat->setNamePath($namePath);
            }
            
            $cache = array();
            foreach ($collection as $cat)
            {
                $cache[strtolower($cat->getNamePath())] = $cat;
                $cat->unsNamePath();
            }
            $this->_categoryCache[$store->getId()] = $cache;
        }
        $cache =& $this->_categoryCache[$store->getId()];
        
        $catIds = array();
        foreach (explode(' , ', $categories) as $categoryPathStr)
        {
            if (!empty($cache[$categoryPathStr]))
            {
                $catIds[] = $cache[$categoryPathStr]->getId();
                continue;
            }
            $path = $rootPath;
            
            $namePath = '';
            foreach (explode('/', $categoryPathStr) as $catName)
            {
                $namePath .= (empty($namePath) ? '' : '/').strtolower($catName);
                if (empty($cache[$namePath]))
                {
                    $cat = Mage::getModel('catalog/category')
                        ->setStoreId($store->getId())
                        ->setPath($path)
                        ->setName($catName)
                        ->setIsActive(1)
                        ->save();
                    $cache[$namePath] = $cat;
                }
                $catId = $cache[$namePath]->getId();
                $path .= '/'.$catId;
            }
            if ($catId)
            {
                $catIds[] = $catId;
            }
        }
        return join(',', $catIds);
    }

}