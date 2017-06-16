<?php
class OTTO_AdvancedFaq_Block_List extends OTTO_AdvancedFaq_Block_Kbase
{
	public function __construct(){
		$this->setTemplate("otto_advancedfaq/articles/theme1/category.phtml");
		return parent::__construct();
	}
}