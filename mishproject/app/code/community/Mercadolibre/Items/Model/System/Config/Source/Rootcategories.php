<?php
class Mercadolibre_Items_Model_System_Config_Source_Rootcategories
{
    protected $_options;

    public function toOptionArray($isMultiselect)
    {
         
		//$siteid = Mage::getStoreConfig("mlitems/mltokenaccess/mlsiteid",Mage::app()->getStore());
		$siteid =  Mage::helper('items')->getMlSiteId();
		if (!$this->_options) {
			$melicategories = Mage::getModel('items/melicategories')
							->getCollection()
							->addFieldToFilter('site_id',$siteid)
							->addFieldToFilter('root_id',0);
			$dataMLRootCat = $melicategories->getData();
            $this->_options = array();
            foreach( $dataMLRootCat as $row ) {
					$this->_options[] = array(
						'label' => $row['meli_category_name'],
						'value' => $row['meli_category_id'],
					);
				}
        }

        $options = $this->_options;
        return $options;
    }

} 