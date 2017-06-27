<?php
/**
* Chweb Multi Layered Navigation 
* 
* @category     Chweb
* @package      Chweb_Mullayernav 
* @copyright    Copyright (c) 2014-2015 Chweb (http://www.chaudharyweb.com/)
* @author       Chweb (Rajesh chaudhary)  
* @version      Release: 1.0.0
* @Class        Chweb_Mullayernav_Block_Layer_Filter_Price  
* @Overwrite    Chweb_Mullayernav_Block_Layer_Filter_Category
*/ 


class Chweb_Mullayernav_Block_Layer_Filter_Categorysearch extends Chweb_Mullayernav_Block_Layer_Filter_Category
{
    public function __construct()
    {

        parent::__construct();
		//Load Custom PHTML of category search
        $this->setTemplate('mullayernav/filter_category_search.phtml');
		//Set Filter Model Name
        $this->_filterModelName = 'mullayernav/layer_filter_categorysearch'; 
    }
    
} 
