<?php
class VES_VendorsReport_Model_Grouped_Collection extends Mage_Reports_Model_Grouped_Collection
{
	public function getResourceCollection(){
		return $this->_resourceCollection;
	}
}