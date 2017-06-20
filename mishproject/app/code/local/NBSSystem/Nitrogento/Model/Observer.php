<?php

class NBSSystem_Nitrogento_Model_Observer
{
    public function validateLicenceKey($observer)
    {
        if (Mage::app()->getRequest()->getParam('section') == 'nitrogento')
        {
            Mage::helper('nitrogento')->validateLicenceKey();
        }
    }
    
    public function addProductDeleteCacheMassAction($observer)
    {
        // Retrieve current block
        $currentBlock = $observer->getBlock();
        if ($currentBlock instanceof Mage_Adminhtml_Block_Catalog_Product_Grid)
        {
            $currentBlock->getMassactionBlock()->addItem('cleanCache', array(
                    'label' => Mage::helper('nitrogento')->__('Clean Product Cache'),
                    'url'   => $currentBlock->getUrl('*/catalog_cleaner/massCleanProductCache')
            ));
        }
    }
    
    public function addCategoryDeleteCacheAction($observer)
    {
        // Retrieve current block
        $currentBlock = $observer->getBlock();
        if ($currentBlock instanceof Mage_Adminhtml_Block_Catalog_Category_Edit_Form)
        {
            $currentBlock->addAdditionalButton("cleanerCategoryCache", array(
                    'label'     => Mage::helper('catalog')->__('Clean Category Cache'),
                    'onclick'   => "cleanCategoryCache('" . $currentBlock->getUrl('*/catalog_cleaner/cleanCategoryCache', array('_current'=>true)). "')",
            ));
    
            $currentBlock->getLayout()->getBlock('js')->append($currentBlock->getLayout()->createBlock('nitrogento/adminhtml_cache_common_category_cleaner'));
        }
    }
    
    public function disableCatalogSessionMemorizeParams()
    {
        Mage::getSingleton('catalog/session')->setParamsMemorizeDisabled(true);
    }
    
    // THIS IS A TRICK USED ONLY IN FRONTEND CAUSE 1.8CE VERSION ADD A CHECK ON THIS FOR ADDING CART PRODUCTS
    // AND THIS CONFLICTS FULLPAGE CACHE FEATURE
    public function disableFormKey()
    {
        Mage::getSingleton('core/session')->setData('_form_key', 'disabled');
    }
    
    public function disableReportsProductDisplay($observer)
    {
        if (Mage::helper("nitrogento")->isCacheFullpageEnabled())
        {
            if (version_compare(Mage::getVersion(), '1.4.0', '>='))
            {
                $observer->getEvent()->getLayout()->getUpdate()->addHandle('nitrogento_disable_reports_product_display_v14');
            }
            else
            {
                $observer->getEvent()->getLayout()->getUpdate()->addHandle('nitrogento_disable_reports_product_display_v13');
            }
        }
    }
    
    public function saveStoremap()
    {
        $storemap = new NBSSystem_Nitrogento_Model_Core_Storemap();
        $storemap->save();
    }
}
