<?php

/* added automatically by conflict fixing tool */
if (Mage::getConfig()->getNode('modules/Magestore_AffiliateplusReferFriend/active')) {

    class VES_VendorsProduct_Block_Catalog_Product_List_Amasty_Pure extends Magestore_AffiliateplusReferFriend_Block_Product_List {
        
    }

} else if (Mage::getConfig()->getNode('modules/MageWorx_InstantCart/active')) {

    class VES_VendorsProduct_Block_Catalog_Product_List_Amasty_Pure extends MageWorx_InstantCart_Block_Catalog_Product_List {
        
    }

} else if (Mage::getConfig()->getNode('modules/MageWorx_DeliveryZone/active')) {

    class VES_VendorsProduct_Block_Catalog_Product_List_Amasty_Pure extends MageWorx_DeliveryZone_Block_Rewrite_Catalog_Product_List {
        
    }

} else {

    class VES_VendorsProduct_Block_Catalog_Product_List_Amasty_Pure extends Mage_Catalog_Block_Product_List {
        
    }

}
?>