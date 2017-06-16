<?php

class VES_VendorsQuote_Block_Email_Quote_Item_Renderer_Bundle extends VES_VendorsQuote_Block_Email_Quote_Item_Renderer
{

    /**
     * Overloaded method for getting list of bundle options
     * Caches result in quote item, because it can be used in cart 'recent view' and on same page in cart checkout
     *
     * @return array
     */
    public function getOptionList()
    {
        $item    = $this->getItem();
        $options = array();
        $product = $item->getProduct();

        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $product->getTypeInstance(true);
        $buyRequest = json_decode($item->getBuyRequest(),true);
        $bundleOptions = $buyRequest['bundle_option'];
        
        // get bundle options
        $bundleOptionsIds = array_keys($bundleOptions);
        $bundleOptionQty = $buyRequest['bundle_option_qty'];
        if ($bundleOptionsIds) {
            /**
            * @var Mage_Bundle_Model_Mysql4_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);


            $bundleSelectionIds = array_values($bundleOptions);

            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds(
                    $bundleSelectionIds,
                    $product
                );

                $bundleOpts = $optionsCollection->appendSelections($selectionsCollection, true);

                foreach ($bundleOpts as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = array(
                            'label' => $bundleOption->getTitle(),
                            'value' => array()
                        );

                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = (isset($bundleOptionQty[$bundleOption->getId()])?$bundleOptionQty[$bundleOption->getId()]:$bundleSelection->getSelectionQty()) * 1;
                            if ($qty) {
                                $option['value'][] = $qty . ' x ' . $this->escapeHtml($bundleSelection->getName())
                                    . ' ' . Mage::helper('core')->currency(
                                        $item->getProduct()->getPriceModel()->getSelectionFinalTotalPrice(
                                            $item->getProduct(),
                                            $bundleSelection,
                                            $item->getRequestedQty() * 1,
                                            $qty,
                                            false,
                                            true)
                                    );
                            }
                        }

                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }

}
