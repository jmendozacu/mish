<?php
/**
* Chweb Multi Layered Navigation 
* 
* @category     Chweb
* @package      Chweb_Mullayernav 
* @copyright    Copyright (c) 2016-2017 Chweb (http://www.chaudharyweb.com/)
* @author       Chweb (Rajesh chaudhary)  
* @version      Release: 1.0.0
* @Class        Chweb_Mullayernav_Block_Layer_View   
*/ 
class Chweb_Mullayernav_Block_Layer_View extends Mage_Catalog_Block_Layer_View {

    protected $_filterBlocks = null;
    protected $_helper = null;
    protected $adv=null;
	const review_FILTER_POSITION = 16; 
    const stock_FILTER_POSITION = 17;   	
    protected $_reviewBlockName='mullayernav/layer_filter_rating';
    public function __construct() {
        parent::__construct();		
        $this->_helper = Mage::helper('mullayernav');
$this->adv=Mage::getStoreConfig('advanced/modules_disable_output/Chweb_Mullayernav');
     }
    
    public function getStateInfo() { 
	
	
	
        $_hlp = $this->_helper;
        //Check the Layered Nav position (Search or Catalog pages)
        $ajaxUrl = '';
        if ($_hlp->isSearch()) {
            $ajaxUrl = Mage::getUrl('mullayernav/front/search');
        } elseif ($cat = $this->getLayer()->getCurrentCategory()) {
            $ajaxUrl = Mage::getUrl('mullayernav/front/category', array('id' => $cat->getId()));
        }


        $ajaxUrl = $_hlp->stripQuery($ajaxUrl);
        $url = $_hlp->getContinueShoppingUrl();

        //Set the AJAX Pagination
        $pageKey = Mage::getBlockSingleton('page/html_pager')->getPageVarName();

        //Get parameters of serach
        $queryStr = $_hlp->getParams(true, $pageKey);
        if ($queryStr)
            $queryStr = substr($queryStr, 1);

        $this->setClearAllUrl($_hlp->getClearAllUrl($url));

        if (false !== strpos($url, '?')) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        return array($url, $queryStr, $ajaxUrl);
    }

    public function bNeedClearAll() {
        return $this->_helper->bNeedClearAll();
    }

    protected function _prepareLayout() {
        $_hlp = $this->_helper;
        // Return an object of current category
        $category = Mage::registry('current_category');

        if ($category) {
            $currentCategoryID = $category->getId();
        } else {
            $currentCategoryID = null;
        }

        // Return session object
        $sessionObject = Mage::getSingleton('catalog/session');
        if ($sessionObject AND $lastCategoryID = $sessionObject->getLastCatgeoryID()) {
            if ($currentCategoryID != $lastCategoryID) {
                Mage::register('new_category', true);
            }
        }
        $sessionObject->setLastCatgeoryID($currentCategoryID);

        //Create Category Blocks    
        $this->createCategoriesBlock();
		//Create rating Blocks
		if(Mage::getStoreConfig('mullayernav/option/active'))
        $this->createRatingBlock();
        //preload setting    
		//for stock_filter
		if(Mage::getStoreConfig('mullayernav/settings/enabled'))
		$this->createStockBlock();
		
        $this->setIsRemoveLinks($_hlp->removeLinks());

        $filterableAttributes = $this->_getFilterableAttributes();
        
        $blocks = array();
        foreach ($filterableAttributes as $attribute) {
            $blockType = 'mullayernav/layer_filter_attribute';
            if ($attribute->getFrontendInput() == 'price') {
                $blockType = 'mullayernav/layer_filter_price';
            }

            $name = $attribute->getAttributeCode() . '_filter';

            $blocks[$name] = $this->getLayout()->createBlock($blockType)
                    ->setLayer($this->getLayer())
                    ->setAttributeModel($attribute);

            $this->setChild($name, $blocks[$name]);
        }
        // echo '<pre>'; print_r($blocks);die;
        foreach ($blocks as $name => $block) {
            $block->init();
        }
		
        $this->getLayer()->apply();
        return Mage_Core_Block_Template::_prepareLayout();
    }

    protected function createCategoriesBlock() {
        
        $_hlp = $this->_helper;
        if ('none' != $_hlp->catStyle()) {
            $categoryBlock = $this->getLayout()->createBlock('mullayernav/layer_filter_category')
                    ->setLayer($this->getLayer())
                    ->init();
            $this->setChild('category_filter', $categoryBlock);
        }
    }

    protected function createStockBlock() {
        
         $reviewBlock = $this->getLayout()->createBlock('mullayernav/layer_filter_stock')
                ->setLayer($this->getLayer())
                ->init();

        $this->setChild('stock_filter', $reviewBlock);
        }
        protected function createRatingBlock() {
        
         $reviewBlock = $this->getLayout()->createBlock($this->_reviewBlockName)
                ->setLayer($this->getLayer())
                ->init();

        $this->setChild('review_filter', $reviewBlock);
        }
   
	 public function getFilters() {

        if(Mage::getStoreConfig('mullayernav/mullayernav/show_nav') == 1) {
            if (is_null($this->_filterBlocks)) {
                $filters = parent::getFilters();
            }
			
			 if (($reviewFilter = $this->_getReviewFilter())) {
				
            // Insert review filter to the self::review_FILTER_POSITION position
            $filters = array_merge(
                array_slice(
                    $filters, 0,self::review_FILTER_POSITION - 1
                ),
                array($reviewFilter),
                array_slice(
                    $filters, self::review_FILTER_POSITION - 1, count($filters) - 1
                )
            );
        } 
			if (($stockFilter = $this->_getStockFilter())) {
				
            // Insert review filter to the self::review_FILTER_POSITION position
            $filters = array_merge(
                array_slice(
                    $filters, 0,self::stock_FILTER_POSITION - 1
                ),
                array($stockFilter),
                array_slice(
                    $filters, self::stock_FILTER_POSITION - 1, count($filters) - 1
                )
            );
        } 
				
        }
        return $filters;
    }
	
    protected function _toHtml() {
        $html = parent::_toHtml();
        if (!Mage::app()->getRequest()->isXmlHttpRequest()) {
            $html = '<div id="catalog-filters">' . $html . '</div>';
        }
        return $html;
    }
/**
     * Get review filter block
     *
     * @return Mage_Catalog_Block_Layer_Filter_review
     */
    protected function _getReviewFilter()
    {
	
	
        return $this->getChild('review_filter');
    }
    protected function _getStockFilter()
    {
	
	
        return $this->getChild('stock_filter');
    }
	 
    
    public function getMode()
    {
        $toolbar = $this->getLayout()->getBlock('product_list_toolbar');
        
        return !empty($toolbar) ? $toolbar->getCurrentMode() : false;
    }
	
}
