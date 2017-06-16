<?php
class Mercadolibre_Items_Model_System_Config_Source_Mageconfigattr
{
    protected $_options;
	public function toOptionArray($isMultiselect)
    {
         
		if (!$this->_options) {
		
			$mageAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
						->addFieldToFilter('is_user_defined',1);
		
			$mageAttributes = $mageAttributes->getData();
			$this->_options = array();
            foreach( $mageAttributes as $row ) {
					$this->_options[] = array(
						'label' => $row['frontend_label'].' ( '.$row['attribute_code'].' )',
						'value' => $row['attribute_id'],
					);
				}
        }
		$options = $this->_options;
        return $options;
    }

} 