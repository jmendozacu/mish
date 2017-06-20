<?php

class VES_VendorsQuote_Block_Email_Quote_Item_Renderer extends Mage_Core_Block_Template{
    protected $_product;
    protected $_productUrl;
    protected $_quote;
    
    /**
     * Get product
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct(){
        return $this->getItem()->getProduct();
    }
    
    /**
     * Get Product Url
     * @return string
     */
    public function getProductUrl(){
        if (!is_null($this->_productUrl)) {
            return $this->_productUrl;
        }
        
        $product = $this->getProduct();
        return $product->getUrlModel()->getUrl($product);
    }
    
    /**
     * Get product name
     */
    public function getProductName(){
        return $this->getItem()->getName();
    }
    
    /**
     * Get requested qty
     */
    public function getQty(){
        return $this->getItem()->getRequestedQty();
    }
    
    /**
     * Get item comment
     */
    public function getComment(){
        return $this->getItem()->getClientComment();
    }
    
    /**
     * Get product thumbnail image
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    public function getProductThumbnail()
    {
        if (!is_null($this->_productThumbnail)) {
            return $this->_productThumbnail;
        }
        return $this->helper('catalog/image')->init($this->getProduct(), 'thumbnail');
    }
    /**
     * Get item original price
     */
    public function getOriginalPrice(){
        return $this->getItem()->getPrice();
    }
    
    /**
     * Get product price
     */
    public function getProductPrice(){
        return $this->getProduct()->getFinalPrice();
    }
    
    /**
     * Get delete item url
     * @return string $url
     */
    public function getDeleteUrl(){
        return $this->getUrl('vquote/index/remove',array('id'=>$this->getItem()->getId()));
    }
    
    /**
     * Retrieves product configuration options
     *
     * @param Mage_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getProductOptions()
    {
        $item    = $this->getItem();
        $product = $item->getProduct();
        $options = array();
        $buyRequest = json_decode($item->getBuyRequest(),true);
        $optionIds = array_keys($buyRequest['options']);
        if ($optionIds) {
            $options = array();
            foreach ($optionIds as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option) {
                    $itemOption = $buyRequest['options'][$optionId];
                    if(!$itemOption) continue;
                    if(is_array($itemOption)) $itemOption = implode(",", $itemOption);
                    $group = $option->groupFactory($option->getType())
                    ->setOption($option)
                    ->setConfigurationItem($item)
                    ->setConfigurationItemOption($itemOption);
                    if ('file' == $option->getType()) {
                        $downloadParams = $item->getFileDownloadParams();
                        if ($downloadParams) {
                            $url = $downloadParams->getUrl();
                            if ($url) {
                                $group->setCustomOptionDownloadUrl($url);
                            }
                            $urlParams = $downloadParams->getUrlParams();
                            if ($urlParams) {
                                $group->setCustomOptionUrlParams($urlParams);
                            }
                        }
                    }
                    $options[] = array(
                        'label' => $option->getTitle(),
                        'value' => $group->getFormattedOptionValue($itemOption),
                        'print_value' => $group->getPrintableOptionValue($itemOption),
                        'option_id' => $option->getId(),
                        'option_type' => $option->getType(),
                        'custom_view' => $group->isCustomizedView()
                    );
                }
            }
        }
    
        $addOptions = $item->getOptionByCode('additional_options');
        if ($addOptions) {
            $options = array_merge($options, unserialize($addOptions->getValue()));
        }
    
        return $options;
    }
    
    /**
     * Get list of all otions for product
     *
     * @return array
     */
    public function getOptionList()
    {
        return $this->getProductOptions();
    }
    
    public function getFormatedOptionValue($optionValue)
    {
        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('catalog/product_configuration');
        $params = array(
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
        );
        return $helper->getFormattedOptionValue($optionValue, $params);
    }
}