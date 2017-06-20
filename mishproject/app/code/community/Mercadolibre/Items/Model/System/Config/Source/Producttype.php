<?php
class Mercadolibre_Items_Model_System_Config_Source_Producttype
{
    protected $_options;

    public function toOptionArray($isMultiselect)
    {
        if (!$this->_options) {
			$productType = Mage::getSingleton('catalog/product_type')->getOptionArray();
            $this->_options = array();
            foreach( $productType as $key => $row ) {
					$this->_options[] = array(
						'label' => $row,
						'value' => $key,
					);
				}
        }
        $options = $this->_options;
		return $options;
    }

}