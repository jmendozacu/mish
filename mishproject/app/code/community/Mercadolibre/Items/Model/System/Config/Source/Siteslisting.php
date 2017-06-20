<?php
class Mercadolibre_Items_Model_System_Config_Source_Siteslisting
{
    protected $_options;

    public function toOptionArray($isMultiselect)
    {
        if (!$this->_options) {
			$melisites = Mage::getModel('items/melisites')->getCollection();
			$dataMLSites = $melisites->getData();
            $this->_options = array();
            foreach( $dataMLSites as $row ) {
					$this->_options[] = array(
						'label' => $row['site_name'],
						'value' => $row['site_id'],
					);
				}
        }

        $options = $this->_options;
		return $options;
    }

}