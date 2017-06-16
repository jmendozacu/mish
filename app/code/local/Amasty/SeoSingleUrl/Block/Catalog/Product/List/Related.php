<?php

class Amasty_SeoSingleUrl_Block_Catalog_Product_List_Related extends Mage_Catalog_Block_Product_List_Related
{
	/**
	 * @return $this
	 */
	protected function _prepareData()
	{
		parent::_prepareData();
		$this->_itemCollection->addUrlRewrite();

		return $this;
	}
}
