<?php
/**
* Chweb Multi Layered Navigation 
* 
* @category     Chweb
* @package      Chweb_Mullayernav 
* @copyright    Copyright (c) 2014-2015 chweb (http://www.chaudharyweb.com/)
* @author       Chweb (Rajesh chaudhary)  
* @version      Release: 1.0.0
* @Class        Chweb_Mullayernav_Model_Select   
*/ 

class Chweb_Mullayernav_Model_Select extends Zend_Db_Select 
{
    public function __construct()
    {
		
    }

    public function setPart($part, $val){
        $this->_parts[$part] = $val;
    }   
} 
