<?php /* added automatically by conflict fixing tool */ if (Mage::getConfig()->getNode('modules/IWD_OrderManager/active')) {
                class VES_VendorsSales_Block_Adminhtml_Sales_Order_Grid_Amasty_Pure extends IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid {}
            } else if (Mage::getConfig()->getNode('modules/EaDesign_PdfGenerator/active')) {
                class VES_VendorsSales_Block_Adminhtml_Sales_Order_Grid_Amasty_Pure extends EaDesign_PdfGenerator_Block_Adminhtml_Block_Sales_Order_Grid {}
            } else { class VES_VendorsSales_Block_Adminhtml_Sales_Order_Grid_Amasty_Pure extends Mage_Adminhtml_Block_Sales_Order_Grid {} } ?>