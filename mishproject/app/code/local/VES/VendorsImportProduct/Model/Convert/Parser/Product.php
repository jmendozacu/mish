<?php

class VES_VendorsImportProduct_Model_Convert_Parser_Product extends Mage_Catalog_Model_Convert_Parser_Product
{
	public function __construct()
    {
    	parent::__construct();
    	foreach (Mage::getConfig()->getNode('admin/import_product/ignore_fields')->asArray() as $code=>$node) {
	    	$this->_systemFields[] = $code;
    	}
    	
    	$notExportedAttributes = explode(",",Mage::getStoreConfig('vendors/vendors_import_export/ignored_attributes'));
    	foreach($notExportedAttributes as $attr){
    		$this->_systemFields[] = $attr;
    	}
    	//foreach()
    }
    
	public function getVendor(){
		return Mage::getSingleton('vendors/session')->getVendor();;
	}
    
	
	/**
     * Unparse (prepare data) loaded products
     *
     * @return Mage_Catalog_Model_Convert_Parser_Product
     */
    public function unparse()
    {
        $entityIds = $this->getData();

        foreach ($entityIds as $i => $entityId) {
            $product = $this->getProductModel()
                ->setStoreId($this->getStoreId())
                ->load($entityId);
            $this->setProductTypeInstance($product);
            /* @var $product Mage_Catalog_Model_Product */

            $position = Mage::helper('catalog')->__('Line %d, SKU: %s', ($i+1), $product->getSku());
            $this->setPosition($position);

            $row = array(
                'store'         => $this->getStore()->getCode(),
                'websites'      => '',
                'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(),
                                        $product->getAttributeSetId()),
                'type'          => $product->getTypeId(),
                /*'category_ids'  => join(',', $product->getCategoryIds())*/
            );

            $categories = $product->getCategoryIds();
            $catNames = array();
            
            $categoryCollection = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name')->addAttributeToFilter('entity_id',array('in'=>$categories));
            foreach($categoryCollection as $category){
                $catNames[] = $category->getName();
            }
            $row['categories'] = implode(',', $catNames);

            if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
                $websiteCodes = array();
                foreach ($product->getWebsiteIds() as $websiteId) {
                    $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                    $websiteCodes[$websiteCode] = $websiteCode;
                }
                $row['websites'] = join(',', $websiteCodes);
            } else {
                $row['websites'] = $this->getStore()->getWebsite()->getCode();
                if ($this->getVar('url_field')) {
                    $row['url'] = $product->getProductUrl(false);
                }
            }

            foreach ($product->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }
				
                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {
					/**for vendor categories */
					if(Mage::getConfig()->getModuleConfig('vendorscategory')->is('active', 'true')) {
						if($attribute->getAttributeCode() == 'vendor_categories') {
							$value = trim($value,',');
							$vendor_categories = explode(',',$value);
							$source = Mage::getModel('vendorscategory/source_category')->toArray($this->getVendor()->getId());
							$label = '';
							foreach($vendor_categories as $count => $id) {
								$label .= $source[$id];
								if($count < count($vendor_categories)-1) $label.=',';
							}
							$row[$field] = $label;continue;
						}
					}
					
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }
				
                /*Vendor should not need to know about sku he will be able to edit the vendor_sku only*/
                if($field=='vendor_sku') $field = 'sku';
                
                $row[$field] = $value;
            }

            if ($stockItem = $product->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) {
                        continue;
                    }
                    $row[$field] = $value;
                }
            }

            foreach ($this->_imageFields as $field) {
                if (isset($row[$field]) && $row[$field] == 'no_selection') {
                    $row[$field] = null;
                }
            }

            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
            $product->reset();
        }

        return $this;
    }
}