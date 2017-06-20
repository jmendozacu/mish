<?php
/**
 * Customer edit block
 *
 * @category   VES
 * @package    VES_Vendors
 * @author     Vnecoms Team <support@vnecoms.com>
 */
class VES_VendorsSelectAndSell_Block_Vendor_Product_Edit extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /*Add class for save and continue edit button and reset button*/
        $editBlock = $this->getLayout()->getBlock('product_edit');
        $tabsAllow = $this->helper('selectandsell')->getAllowTabForSell();
        $attributeAllow = $this->helper('selectandsell')->getAllowAttributeForSell();
        $allow_attributes = array();

        if($editBlock){
            $editBlock->setHeader($this->__('Sell product from "%s"', Mage::registry('product')->getName()))->setTemplate('ves_vendorsproduct/product/edit.phtml');

            if($deleteBtn = $editBlock->getChild('delete_button'))
                $editBlock->unsetChild('delete_button');
            if($dupBtn = $editBlock->getChild('duplicate_button'))
                $editBlock->unsetChild('duplicate_button');
            if($backBtn = $editBlock->getChild('back_button'))
                $backBtn->setData('onclick',"setLocation('".Mage::getUrl('*/vendor_selectandsell/select')."')");

            if($saveBtn = $editBlock->getChild('save_button'))
                $saveBtn->setData('onclick',"productForm.submit();")
                    ->setData('label',Mage::helper('core')->__('Save Sell Product'));

            if($saveAndEditBtn = $editBlock->getChild('save_and_edit_button'))
                $editBlock->unsetChild('save_and_edit_button');

            if($resetBtn = $editBlock->getChild('reset_button'))
                $editBlock->unsetChild('reset_button');
        }

        $tabBlock = $this->getLayout()->getBlock('product_tabs');
        if(!$tabBlock) return parent::_prepareLayout();

        /*Remove some tabs*/
        $product = $tabBlock->getProduct();
        $setId = $product->getAttributeSetId();

        foreach(Mage::helper('selectandsell')->getAllowAttributeForSell() as $code=>$info) {
            if($this->helper('selectandsell')->isSellController()) {
                if($info['keep_value'] == '0') $product->setData($code,'');
            }
        }

        $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();
        foreach ($groupCollection as $group) {
            if(!in_array($group->getData('attribute_group_name'), array('General'))){
                $tabBlock->removeTab('group_'.$group->getId());
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
                $tabBlock->setTabData('group_'.$group->getId(),'content',$this->_translateHtml($this->getLayout()->createBlock('vendorsproduct/vendor_product_edit_tab_attributes')->setGroup($group)->setGroupAttributes($attributes)->toHtml()));
            }

        }
        if ($setId) {
            $tabBlock->removeTab('websites');
            $tabBlock->removeTab('upsell');
            $tabBlock->removeTab('crosssell');
            $tabBlock->removeTab('categories');
            $tabBlock->removeTab('related');
            $tabBlock->removeTab('customer_options');
            $tabBlock->removeTab('vendor_categories');
            $tabBlock->removeTab('inventory');
            $tabBlock->removeTab('configurable');
            

            /*if (Mage::app()->getStore()->isCurrentlySecure()){
                $link_custom = Mage::getUrl('vendors/catalog_product/options', array('_current' => true,'_secure'=>true));
            }
            else{
                $link_custom = Mage::getUrl('vendors/catalog_product/options', array('_current' => true));
            }
            if (!$product->isGrouped()) {
                $tabBlock->addTab('customer_options', array(
                    'label' => Mage::helper('catalog')->__('Custom Options'),
                    'url'   => $link_custom,
                    'class' => 'ajax',
                ));
            }*/
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
                $tabBlock->setTabData('group_'.$group->getId(),'content',$this->_translateHtml(Mage::app()->getLayout()->createBlock('vendorsproduct/vendor_product_edit_tab_attributes')->setGroup($group)->setGroupAttributes($general_attributes)->toHtml()));
            }
        }

        return $this;
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
