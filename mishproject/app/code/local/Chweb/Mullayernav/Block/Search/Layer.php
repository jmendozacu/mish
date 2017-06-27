<?php
/**
* Chweb Multi Layered Navigation 
* 
* @category     Chweb
* @package      Chweb_Mullayernav 
* @copyright    Copyright (c) 2016-2017 Chweb (http://www.chaudharyweb.com/)
* @author       Chweb (Rajesh chaudhary)  
* @version      Release: 1.0.0
* @Class        Chweb_Mullayernav_Block_Search_Layer   
*/
class Chweb_Mullayernav_Block_Search_Layer extends Chweb_Mullayernav_Block_Layer_View {

    public function getLayer() {
        return Mage::getSingleton('catalogsearch/layer');
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock() {

        $availableResCount = (int) Mage::app()->getStore()
                        ->getConfig(Mage_CatalogSearch_Model_Layer::XML_PATH_DISPLAY_LAYER_COUNT);

        if (!$availableResCount || ($availableResCount >= $this->getLayer()->getProductCollection()->getSize())) {
            return parent::canShowBlock();
        }
        return false;
    }

    protected function createCategoriesBlock() {

        $categoryBlock = $this->getLayout()
                ->createBlock('mullayernav/layer_filter_categorysearch')
                ->setLayer($this->getLayer())
                ->init();
        $this->setChild('category_filter', $categoryBlock);
    }

}
