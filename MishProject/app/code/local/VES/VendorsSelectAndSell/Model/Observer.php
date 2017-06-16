<?php
class VES_VendorsSelectAndSell_Model_Observer extends VES_VendorsSelectAndSell_Model_Inventory {
    public function core_block_abstract_prepare_layout_before($ob) {
        if($ob->getBlock() instanceof VES_VendorsProduct_Block_Vendor_Product) {
           $block = $ob->getBlock();
            $block->addButton('select_and_sell', array(
                'label'   => Mage::helper('catalog')->__('Select And Sell'),
                'onclick' => "setLocation('".Mage::getUrl('*/vendor_selectandsell/select')."')",
                'class'   => 'button'
            ));
        }
    }

    public function prepareTabsBlock($block) {
      //  exit;
        $product = $block->getProduct();
        $setId = $product->getAttributeSetId();
        $attributeAllow = Mage::helper('selectandsell')->getAllowAttributeForSell();
        $allow_attributes = array();
/*        foreach(Mage::helper('selectandsell')->getAllowAttributeForSell() as $code=>$info) {
            if($info['keep_value'] == '0') $product->setData($code,'');
        }*/
        $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();
        foreach ($groupCollection as $group) {
            if(!in_array($group->getData('attribute_group_name'), array('General'))){
                $block->removeTab('group_'.$group->getId());
            }

            if(true){
                $attributes = $product->getAttributes($group->getId(), true);
                foreach ($attributes as $key => $attribute) {
                    if(!in_array($attribute->getData('attribute_code'),array_keys($attributeAllow))) {
                        unset($attributes[$key]);
                    }
                    if(in_array($attribute->getData('attribute_code'),array_keys($attributeAllow))) {
                        //save in $allow_attributes
                        $allow_attributes[$attribute->getData('attribute_code')] = $attribute;
                    }
                }

                if (count($attributes)==0) {
                    continue;
                }
                if($group->getData('attribute_group_name') == 'General') {
                    $general_attributes = $attributes;
                }
                $block->setTabData('group_'.$group->getId(),'content',$this->_translateHtml(Mage::app()->getLayout()->createBlock('vendorsproduct/vendor_product_edit_tab_attributes')->setGroup($group)->setGroupAttributes($attributes)->toHtml()));
            }
        }
        if ($setId) {
            $block->removeTab('websites');
            $block->removeTab('upsell');
            $block->removeTab('crosssell');
            $block->removeTab('categories');
            $block->removeTab('related');
            $block->removeTab('customer_options');
            $block->removeTab('vendor_categories');
            $block->removeTab('inventory');
        }
        //set tab content again for general tab
        foreach($groupCollection as $group) {
            if($group->getData('attribute_group_name') == 'General') {
                $attributes = $product->getAttributes($group->getId(), true);
                $key_attributes = $this->arrayColumn($attributes, 'attribute_code');
                $arr_diff = array_diff(array_keys($attributeAllow),$key_attributes);
                foreach($arr_diff as $diff) {
                    $general_attributes[] = $allow_attributes[$diff];
                }
                $block->setTabData('group_'.$group->getId(),'content',$this->_translateHtml(Mage::app()->getLayout()->createBlock('vendorsproduct/vendor_product_edit_tab_attributes')->setGroup($group)->setGroupAttributes($general_attributes)->toHtml()));
            }
        }
    }

    public function core_block_abstract_prepare_layout_after($ob) {
        $block = $ob->getEvent()->getBlock();
        if ($block instanceof VES_VendorsProduct_Block_Vendor_Product_Edit_Tabs) {
            $product = $block->getProduct();
            //if(Mage::helper('selectandsell')->isSoldProduct($product)) $this->prepareTabsBlock($block);
        }
    }

    /**
     *
     * @param $ob
     */
    public function adminhtml_catalog_product_edit_prepare_form($ob) {
        //return;
        $action = Mage::app()->getRequest();
        if(($action->getActionName()=='load' and $action->getControllerName()=='vendor_selectandsell' and $action->getModuleName()=='vendors'))
            //or ($action->getActionName()=='edit' and $action->getControllerName()=='catalog_product' and $action->getModuleName()=='vendors'))
            /**
             *  fix 28-3-15: only select and sell load product to remove some tab,other normally.
             */
        {

            $isEditProduct = false;
            if($action->getActionName()=='edit' and $action->getControllerName()=='catalog_product' and $action->getModuleName()=='vendors') {
                $isEditProduct = true;
            }

            $form = $ob->getForm();
            $allowSelectAndSellAttributes = Mage::helper('selectandsell')->getAllowAttributeForSell();
            $product = $this->getProduct();
            $setId = $product->getAttributeSetId();
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();

            $fieldset = null;
            foreach($groupCollection as $group) {
                if($group->getData('attribute_group_name') == 'General') {
                    $fieldset = $form->getElement('group_fields'.$group->getId());
                }
            }
            if($fieldset) {
                $fieldset->addField('pricecomparison', 'hidden', array(
                        'name' => 'pricecomparison',
                        'label' => Mage::helper('core')->__(''),
                        'title' => Mage::helper('core')->__(''),
                        'class'     => '',
                        'value'     => Mage::app()->getRequest()->getParam('product_id'),
                    )
                );
            }

            /**
             * for all field of inventory tab
             */
            if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
                /**
                 * for qty field
                 */
                if(is_array($allowSelectAndSellAttributes['qty'])) {
                    $qtyInfo = $allowSelectAndSellAttributes['qty'];
                    if($fieldset) {
                        $fieldset->addField('qty', 'text', array(
                                'name' => 'stock_data[qty]',
                                'label' => Mage::helper('core')->__('Qty'),
                                'title' => Mage::helper('core')->__('Qty'),
                                'required'  => true,
                                'class'     => 'required-entry input-text required-entry validate-number',
                                'value'     => ($qtyInfo['keep_value'] == '1' or $isEditProduct)?$product->getStockItem()->getQty()*1:'',
                            )
                        );
                    }
                }

                /**
                 * for stock availability field
                 */
                if(is_array($allowSelectAndSellAttributes['is_in_stock'])) {
                    $stockInfo = $allowSelectAndSellAttributes['is_in_stock'];
                    if($fieldset) {
                        $fieldset->addField('is_in_stock', 'select', array(
                                'name' => 'stock_data[is_in_stock]',
                                'label' => Mage::helper('core')->__('Stock Availability'),
                                'title' => Mage::helper('core')->__('Stock Availability'),
                                'required'  => false,
                                'class'     => '',
                                'values'    => Mage::getSingleton('cataloginventory/source_stock')->toOptionArray(),
                                'value'     => ($stockInfo['keep_value'] or $isEditProduct)?$this->getFieldValue('is_in_stock'):'',
                            )
                        );
                    }
                }
            }
        }
    }

    public function catalog_product_load_after($ob) {
        $product = $ob->getProduct();
        $action = Mage::app()->getRequest();
        if($action->getActionName()=='load' and $action->getControllerName()=='vendor_selectandsell' and $action->getModuleName()=='vendors') {
            $allowAttributes = Mage::helper('selectandsell')->getAllowAttributeForSell();
        }
    }

    public function catalog_product_save_before(Varien_Event_Observer $observer){
		if(!Mage::helper('core')->isModuleEnabled('VES_VendorsPriceComparison')) return;
        $product = $observer->getProduct();
        $postData = Mage::app()->getFrontController()->getRequest()->getParam('product');
        if(!is_array($postData)) return;
        if($relatedProductId = $postData['pricecomparison']){
            if($product->getId() == $relatedProductId) return;
			
			$relatedProduct = Mage::getModel('catalog/product')->load($relatedProductId);
			if(!$relatedProduct->getData('ves_enable_comparison')){
			    $relatedProduct->setData('ves_enable_comparison',1)->getResource()->saveAttribute($relatedProduct, 'ves_enable_comparison');
			}
			if($relatedProduct->getId()) $product->setData('vendor_parent_product',$relatedProductId);
        }
    }

    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }

    /**
     * get array of field of array ( 2 dimension)
     * @param $array
     * @param $field
     */
    public function arrayColumn($array,$field) {
        $result = array();
        foreach($array as $key => $_array) {
            if(is_array($_array)) $result[] = $_array[$field];
            else if(is_object($_array)) {
                $result[] = $_array->getData($field);
            }
        }
        return $result;
    }
}