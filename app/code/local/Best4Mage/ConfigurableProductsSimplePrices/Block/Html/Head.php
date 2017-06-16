<?php

class Best4Mage_ConfigurableProductsSimplePrices_Block_Html_Head extends Mage_Page_Block_Html_Head {
	
    public function addItem($type, $name, $params=null, $if=null, $cond=null)
    {
        if ($type==='skin_css' && empty($params)) {
            $params = 'media="all"';
        }

        if($type==='skin_js' && $name==="js/configurableswatches/swatches-product.js") {
        	if(Mage::helper('core')->isModuleEnabled('Mage_ConfigurableSwatches') || !Mage::helper('core')->isModuleEnabled('Mage_ConfigurableSwatches')) { 
				if(!Mage::helper('configurableswatches')->isEnabled()){ 
					return $this;
				}
			}
        }

        $this->_data['items'][$type.'/'.$name] = array(
            'type'   => $type,
            'name'   => $name,
            'params' => $params,
            'if'     => $if,
            'cond'   => $cond,
       );
        return $this;
    }
}