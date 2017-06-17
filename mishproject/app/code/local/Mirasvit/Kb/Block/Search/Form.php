<?php
class Mirasvit_Kb_Block_Search_Form extends Mage_Core_Block_Template
{
	public function getQuery()
	{
		return Mage::registry('search_query');
	}
}