<?php
class Mercadolibre_Items_Model_System_Config_Source_Defaultstore
{
    protected $_options;

    public function toOptionArray($isMultiselect)
    {
		/*
		if (!$this->_options) {
			foreach (Mage::app()->getWebsites() as $website){
				foreach ($website->getGroups() as $group){
					$stores = $group->getStores();
					foreach ($stores as $store){
						$this->_options[] = array(
						'label' => $store->getName(),
						'value' => $store->getId(),
						);
					}
        		}
			}
		}
		$options = $this->_options; 
		*/
		return Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false); //$options;
    }

}