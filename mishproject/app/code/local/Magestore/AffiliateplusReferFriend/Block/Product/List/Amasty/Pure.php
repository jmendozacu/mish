<?php /* added automatically by conflict fixing tool */ if (Mage::getConfig()->getNode('modules/MageWorx_InstantCart/active')) {
                class Magestore_AffiliateplusReferFriend_Block_Product_List_Amasty_Pure extends MageWorx_InstantCart_Block_Catalog_Product_List {}
            } else if (Mage::getConfig()->getNode('modules/MageWorx_DeliveryZone/active')) {
                class Magestore_AffiliateplusReferFriend_Block_Product_List_Amasty_Pure extends MageWorx_DeliveryZone_Block_Rewrite_Catalog_Product_List {}
            } else { class Magestore_AffiliateplusReferFriend_Block_Product_List_Amasty_Pure extends Mage_Catalog_Block_Product_List {} } ?>