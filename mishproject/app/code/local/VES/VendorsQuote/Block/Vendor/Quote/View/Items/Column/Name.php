<?php

class VES_VendorsQuote_Block_Vendor_Quote_View_Items_Column_Name extends VES_VendorsQuote_Block_Vendor_Quote_View_Items_Column_Default
{
    /**
     * Retrieves product configuration options
     *
     * @param Mage_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getQuoteOptions()
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
     * Add line breaks and truncate value
     *
     * @param string $value
     * @return array
     */
    public function getFormattedOption($value)
    {
        $_remainder = '';
        $value = Mage::helper('core/string')->truncate($value, 55, '', $_remainder);
        $result = array(
            'value' => nl2br($value),
            'remainder' => nl2br($_remainder)
        );
    
        return $result;
    }
}